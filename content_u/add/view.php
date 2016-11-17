<?php
namespace content_u\add;

class view extends \mvc\view
{

	function config()
	{
		$this->include->fontawesome = true;
		// check permisson
		if($this->access('u', 'complete_profile', 'admin'))
		{
			$this->data->profile_lock = array_keys(\lib\db\filters::support_filter());
		}

	}

	function view_edit($_args)
	{
		$poll_id = $_args->api_callback;
		$poll = \lib\db\polls::get_poll($poll_id);
		$this->data->poll = $poll;
		$answers = \lib\utility\answers::get($poll_id);
		$this->data->answers = $answers;

	}

	/**
	 * ready to load add poll
	 */
	function view_add()
	{

	}


	/**
	 * ready to load survey mode
	 */
	function view_survey($_args)
	{
		// enable survey mod to load buttom and something else
		$this->data->survey_mod = true;
		// get survery id from url
		$survey_id = $this->model()->check_poll_url($_args);
		// get list of poll in this survey
		$poll_list = \lib\utility\survey::get_poll_list($survey_id);
		$this->data->poll_list = $poll_list;
	}
}
?>