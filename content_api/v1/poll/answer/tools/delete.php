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
		// start transaction
		// \lib\db::transaction();
		// set debug title
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

		$args =
		[
			'poll_id'    =>  $_options['id'],
			'user_id'    => $this->user_id,
			'old_answer' => $my_answer,
		];

		\lib\utility\answers::delete($args);

		if(debug::$status)
		{
			// \lib\db::commit();
		}
		else
		{
			// \lib\db::rollback();
		}

		return;
	}
}
?>