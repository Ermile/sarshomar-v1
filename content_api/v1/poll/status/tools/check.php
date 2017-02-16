<?php
namespace content_api\v1\poll\status\tools;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait check
{


	/**
	 * no body answer to this poll
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	protected static function answer_one_person($_poll_id)
	{
		$query  = "SELECT polldetails.id AS `count` FROM polldetails WHERE polldetails.post_id = $_poll_id LIMIT 1";
		$result = db::get($query,'count', true);
		$result = intval($result);
		if($result)
		{
			return true;
		}
		return false;
	}


	/**
	 * check poll syntax
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	protected static function check($_options = [])
	{
		if(!debug::$status)
		{
			return;
		}

		$words = [];

		$poll = db\polls::get_poll($_options['poll_id']);

		if(!isset($poll['title']) || (isset($poll['title']) && !$poll['title'] || $poll['title'] == '‌'))
		{
			return debug::error(T_("Poll title not set"), 'title', 'arguments');
		}

		if(!isset($poll['language']) || (isset($poll['language']) && !$poll['language']))
		{
			return debug::error(T_("Poll language not set"), 'language', 'arguments');
		}

		$answers = db\pollopts::get($_options['poll_id']);

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

		if((in_array('upload', $type) || in_array('range', $type) || in_array('notification', $type)) && count($answers) > 1)
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
				return debug::error(T_("Required minimum answers for select polls is two answers"), 'answers', 'arguments');
			}

			if($value['type'] == 'select')
			{
				if(array_key_exists('title', $value) && array_key_exists('attachment', $value))
				{

					if(!$value['title'] && !$value['attachment'])
					{
						return debug::error(T_("You must set title of the answer or set file in index :key of answer", ['key' => $key]), 'answers', 'arguments');
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

		if(isset($poll['sarshomar']) && $poll['sarshomar'] === '1')
		{
			$set_status_awaiting = true;
			debug::warn(T_("You poll is awaiting moderation, all Sarshomar polls are set in awaiting moderation"));
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
			'user_id'    => $_options['user_id'],
			'login'      => $_options['user_id'],
			'in'         => 'me',
		];

		$check_duplicate_poll_title = db\polls::search(null, $check_duplicate_poll_title);

		if(count($check_duplicate_poll_title) > 1)
		{
			debug::warn(T_("Duplicate poll title of your poll"), 'title', 'arguments');
		}

		if($set_status_awaiting)
		{
			db\polls::update(['post_status' => 'awaiting'], $_options['poll_id']);
		}
	}
}

?>