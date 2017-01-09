<?php 
namespace lib\db\polls\insert;
use \lib\debug;

trait check
{
	protected static function ready()
	{
		if(self::$update_mod)
		{
			if(!self::is_my_poll(self::$poll_id, self::$user_id))
			{
				return debug::error(T_("This is not your poll, can't update"), 'id', 'permission');
			}
			self::$saved_poll = \lib\db\polls::get_poll(self::$poll_id);
		}	
	}

	protected static function check()
	{
		if(self::$publish_mod)
		{	
			// check answers
			if(is_null(self::$args['answers']) || empty(self::$args['answers']) || !is_array(self::$args['answers']))
			{
				return \lib\debug::error(T_("invalid parametr answers"), 'answers', 'arguments');
			}
			// check answers	
			if(!isset(self::$args['answers'][0]['type']) || (isset(self::$args['answers'][0]['type']) && empty(self::$args['answers'][0]['type'])))
			{
				return \lib\debug::error(T_("invalid parametr answer type"), 'type', 'arguments');
			}
			// set the answer type
			$answer_type = self::$args['answers'][0]['type'];

			if(
				isset(self::$args['options']['survey_id']) && 
				self::$args['options']['survey_id'] && 
				!preg_match("/^[". $shortURL. "]+$/", self::$args['options']['survey_id'])
			  )
			{
				return \lib\debug::error(T_("invalid parametr survey_id"), 'survey_id', 'arguments');
			}

			// check the article id
			if(
				isset(self::$args['options']['article']) && 
				self::$args['options']['article'] && 
				!preg_match("/^[". $shortURL. "]+$/", self::$args['options']['article'])
			  )
			{
				return \lib\debug::error(T_("invalid parametr article"), 'article', 'arguments');
			}

			if($publish_mod)
			{
				// save and check words
				if(!\lib\db\words::save_and_check(self::$args))
				{
					$poll_status = 'awaiting';
					\lib\debug::warn(T_("You are using an inappropriate word in the text, your poll is awaiting moderation"), 'words', 'arguments');
					// plus the userrank of usespamword
					\lib\db\userranks::plus(self::$args['user'], 'usespamword');
				}
			}
		}
	}
}
?>