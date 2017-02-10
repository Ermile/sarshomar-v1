<?php
namespace content_api\v1\poll\answer\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get
{
	/**
	 * get pollanswer
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_answer_get($_options = [])
	{
		if(!shortURL::is(utility::request('id')))
		{
			return debug::error(T_("Invalid parameter id"), 'id', 'arguments');
		}

		$poll_id = utility\shortURL::decode(utility::request('id'));

		if($poll_id)
		{
			$is_answer = \lib\utility\answers::is_answered($this->user_id, $poll_id);
			if($is_answer)
			{
				debug::title(T_("You was answer to this poll"));
				return true;
			}
			debug::title(T_("You was not answer to this poll"));
			return false;
		}
	}
}
?>