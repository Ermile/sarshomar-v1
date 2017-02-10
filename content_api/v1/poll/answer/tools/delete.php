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
		$default_options = ['id' => null];

		$_options = array_merge($default_options, $_options);

		$get_poll_options =
		[
			'check_is_my_poll'   => false,
			'get_filter'         => false,
			'get_opts'           => false,
			'get_options'	     => false,
			'run_options'	     => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'type'               => null, // ask || random
		];
		$poll = $this->poll_get($get_poll_options);

		if(!$poll)
		{
			return debug::error(T_("Poll not found"), 'poll', 'url');
		}

		$poll_id = $_options['id'];

		$result = \lib\db\polldetails::remove($this->user_id, $poll_id);
		if($result)
		{
			debug::title(T_("Your answer was delete"));
		}
		return;
	}
}
?>