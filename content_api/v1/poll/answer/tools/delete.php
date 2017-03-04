<?php
namespace content_api\v1\poll\answer\tools;
use \lib\utility;
use \lib\debug;

trait delete
{
	/**
	 * delete pollanswer
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_answer_delete($_options = [])
	{
		debug::title(T_("Can not remove your answer"));
		// because the id in url not in request
		$default_options = ['id' => null];

		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);
		$_options['id'] = utility\shortURL::decode($_options['id']);

		$answer = $this->poll_answer_get($_options);

		$my_answer = [];
		if(isset($answer['my_answer']))
		{
			$my_answer = $answer['my_answer'];
		}

		if(isset($answer['available']) && is_array($answer['available']))
		{
			if(!in_array('delete', $answer['available']))
			{
				return debug::error(T_("Can not remove your answer"), 'answer', 'permission');
			}
		}
		else
		{
			return debug::error(T_("Invalid answer available"), 'api', 'system');
		}

		$poll_id    = $_options['id'];

		$old_answer = \lib\db\polldetails::get($this->user_id, $poll_id);
		if(!is_array($old_answer))
		{
			$old_answer = [];
		}

		foreach ($my_answer as $key => $value)
		{
			$validation = 'invalid';
			$profile    = 0;
			foreach ($old_answer as $k => $v)
			{
				if(isset($v['opt']) && $v['opt'] == $key)
				{
					if(isset($v['validstatus']))
					{
						$validation = $v['validstatus'];
					}
					if(isset($v['profile']))
					{
						$profile = $v['profile'];
					}
				}
			}

			$answers_details =
			[
				'poll_id'    => $poll_id,
				'opt_key'    => $key,
				'user_id'    => $this->user_id,
				'type'       => 'minus',
				'profile'    => $profile,
				'validation' => $validation
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		$result = \lib\db\polldetails::remove($this->user_id, $poll_id);

		if($result && \lib\db::affected_rows())
		{
			debug::title(T_("Your answer has been deleted"));
		}
		else
		{
			return debug::error(T_("You have not answered to this poll"));
		}
		return;
	}
}
?>