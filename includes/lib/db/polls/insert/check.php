<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\db;
use \lib\utility\shortURL;

trait check
{

	public static function is_attachment($_code)
	{

		if(!preg_match("/^[". self::$args['shortURL']. "]+$/", $_code))
		{
			return debug::error(T_("Invalid parameter file"), 'file', 'arguments');
		}

		$attachment_id = shortURL::decode($_code);
		$attachment = self::get_poll($attachment_id);
		if(!$attachment)
		{
			return debug::error(T_("Attachment not found"), 'file', 'arguments');
		}

		if(!isset($attachment['type']) || (isset($attachment['type']) && $attachment['type'] != 'attachment'))
		{
			return debug::error(T_("This is not an attachment record"), 'file', 'arguments');
		}

		if(isset($attachment['status']))
		{
			switch ($attachment['status'])
			{
				case 'draft':
				case 'awaiting':
				case 'publish':
					// no thing !
					break;

				case 'stop':
				case 'pause':
				case 'trash':
				case 'deleted':
				case 'filtered':
				case 'blocked':
				case 'spam':
				case 'violence':
				case 'pornography':
				case 'schedule':
				case 'expired':
				case 'filter':
				default:
					return debug::error(T_("Can not use this attachment"), 'file', 'permission');
					break;
			}
		}
		$url = null;
		if(isset($attachment['meta']['url']))
		{
			$url = $attachment['meta']['url'];
		}
		return $url;
	}

	/**
	 * no body answer to this poll
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	protected static function no_body_answer()
	{
		return true;
	}


	/**
	 * check poll syntax
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	protected static function check()
	{
		if(self::$draft_mod)
		{
			// in draft mod we not check every thing
			return;
		}

		if(!debug::$status)
		{
			return;
		}

		$words = [];
		$poll = db\polls::get_poll(self::$poll_id);

		if(!isset($poll['title']) || (isset($poll['title']) && !$poll['title'] || $poll['title'] == '~'))
		{
			return debug::error(T_("Poll title not set"), 'title', 'arguments');
		}

		if(!isset($poll['language']) || (isset($poll['language']) && !$poll['language']))
		{
			return debug::error(T_("Poll language not set"), 'language', 'arguments');
		}

		$answers = db\pollopts::get(self::$poll_id);

		if(!$answers || !is_array($answers))
		{
			return debug::error(T_("Answers can not be empty"), 'answers', 'arguments');
		}

		$type = array_column($answers, 'type');

		$count_value = array_count_values($type);

		if(isset($count_value['descriptive']) && $count_value['descriptive'] > 1)
		{
			return debug::error(T_("Can not set more than 1 descriptive answers"), 'answers', 'arguments');
		}

		if(
			(
				in_array('upload', $type) ||
				in_array('range', $type) ||
				in_array('notification', $type)
			) &&
			count($answers) > 1
		 )
		{
			return debug::error(T_("Can not set more than 1 answers in upload,range and notification mod"), 'answers', 'arguments');
		}

		$answers_have_attachment = false;

		foreach ($answers as $key => $value)
		{
			if(!isset($value['type']) || (isset($value['type']) && !$value['type']))
			{
				return debug::error(T_("Answer type not found in index :key of answers" , ['key' => $key]), 'answers', 'arguments');
			}
			if($value['type'] == 'select' && count($answers) <= 1)
			{
				return debug::error(T_("You must set two answers in select mod"), 'answers', 'arguments');
			}
			if($value['type'] == 'select')
			{
				if(array_key_exists('title', $value) && array_key_exists('attachment', $value))
				{

					if(!$value['title'] && !$value['attachment'])
					{
						return debug::error(T_("You must set answer title or set file in index :key of answer", ['key' => $key]), 'answers', 'arguments');
					}
				}
			}
			if(isset($value['title']))
			{
				array_push($words, $value['title']);
			}

			if(isset($value['description']))
			{
				array_push($words, $value['description']);
			}

			if(isset($value['group_score']))
			{
				array_push($words, $value['group_score']);
			}

			if(isset($value['sub_type']))
			{
				array_push($words, $value['sub_type']);
			}

			if(isset($value['attachment']) && $value['attachment'])
			{
				$answers_have_attachment = true;
			}
		}

		if(isset($poll['title']))
		{
			array_push($words, $poll['title']);
		}

		if(isset($poll['content']))
		{
			array_push($words, $poll['content']);
		}

		if(isset($poll['meta']))
		{
			array_push($words, $poll['meta']);
		}

		$set_status_awaiting = false;

		if(isset($poll['privacy']) && $poll['privacy'] == 'public')
		{
			$set_status_awaiting = true;
			debug::warn(T_("You poll is awaiting moderation"));
		}

		// save and check words
		if(!db\words::save_and_check($words))
		{
			$set_status_awaiting = true;
			debug::warn(T_("You are using an inappropriate word in the text, your poll is awaiting moderation"), 'words', 'arguments');
			if(!self::$update_mod)
			{
				// plus the userrank of usespamword
				db\userranks::plus(self::$args['user'], 'usespamword');
			}
		}

		$check_duplicate_poll_title =
		[
			'post_title' => $poll['title'],
			'user_id'    => self::$user_id,
			'in'         => 'me',
		];

		$check_duplicate_poll_title = self::search(null, $check_duplicate_poll_title);

		if(count($check_duplicate_poll_title) > 1)
		{
			debug::warn(T_("Duplicate poll title of your poll"), 'title', 'arguments');
		}

		if($set_status_awaiting)
		{
			db\polls::update(['post_status' => 'awaiting'], self::$poll_id);
		}

		// $option_key   = [];
		// $poll_options = db\posts::get_post_meta(self::$poll_id);
		// if(is_array($poll_options))
		// {
		// 	$option_key = array_column($poll_options, 'option_key');
		// }

		// if(in_array('title_attachment', $option_key) || $answers_have_attachment)
		// {
		// 	if($answers_have_attachment)
		// 	{
		// 		$in      = 'answer';
		// 		$element = 'answers';
		// 		$group   = 'arguments';
		// 	}
		// 	else
		// 	{
		// 		$in      = 'poll';
		// 		$element = 'file';
		// 		$group   = 'arguments';
		// 	}

		// 	db\polls::update(['post_status' => 'awaiting'], self::$poll_id);
		// 	debug::warn(T_("You are using multi media in :in, your poll is awaiting moderation", ['in' => $in]), $element, $group);
		// }
	}
}
?>