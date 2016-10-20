<?php
namespace content_u\add;

class view extends \mvc\view
{

	/**
	 * ready to load add poll
	 */
	function view_add()
	{
		$this->include->fontawesome = true;
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
		$poll_list = \lib\db\survey::get_poll_list($survey_id);
		$this->data->poll_list = $poll_list;
	}
}
?>