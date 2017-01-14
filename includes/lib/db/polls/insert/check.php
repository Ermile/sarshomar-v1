<?php 
namespace lib\db\polls\insert;
use \lib\debug;

trait check

{
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

		if(!\lib\debug::$status)
		{
			return;
		}

		$words = [];
		$poll = \lib\db\polls::get_poll(self::$poll_id);

		if(!isset($poll['title']) || (isset($poll['title']) && !$poll['title'] || $poll['title'] == '~'))
		{
			return debug::error(T_("Poll title not set"), 'title', 'arguments');
		}
		
		if(!isset($poll['language']) || (isset($poll['language']) && !$poll['language']))
		{
			return debug::error(T_("Poll language not set"), 'language', 'arguments');
		}

		$answers = \lib\db\pollopts::get(self::$poll_id);

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
		
		// save and check words
		if(!\lib\db\words::save_and_check($words))
		{
			\lib\db\polls::update(['post_status' => 'awaiting'], self::$poll_id);
			\lib\debug::warn(T_("You are using an inappropriate word in the text, your poll is awaiting moderation"), 'words', 'arguments');
			if(!self::$update_mod)
			{			
				// plus the userrank of usespamword
				\lib\db\userranks::plus(self::$args['user'], 'usespamword');
			}
		}

		// $option_key   = [];
		// $poll_options = \lib\db\posts::get_post_meta(self::$poll_id);
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

		// 	\lib\db\polls::update(['post_status' => 'awaiting'], self::$poll_id);
		// 	\lib\debug::warn(T_("You are using multi media in :in, your poll is awaiting moderation", ['in' => $in]), $element, $group);
		// }
	}
}
?>