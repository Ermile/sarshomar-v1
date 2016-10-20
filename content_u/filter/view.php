<?php
namespace content_u\filter;

class view extends \mvc\view
{

	/**
	 * ready to load fieter page
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_filter($_args)
	{
		// get poll_id || suervey_id from url
		$poll_survey_id = $this->model()->check_poll_url($_args);

		// check is_survey or no
		// if(!\lib\db\survey::is_survey($poll_survey_id))
		// {
		// 	// if user remove polls and redirect to this page
		// 	// we change the survey to poll and redirect to poll/filter
		// 	$url = \lib\db\survey::change_to_poll($poll_survey_id);
		// 	if(is_string($url))
		// 	{
		// 		$this->redirector()->set_url("@/$url/filter")->redirect();
		// 	}
		// }

		// save poll_id to form
		$this->data->poll_id = $poll_survey_id;
		// get existing filter list to load in html and user can select this
		$this->data->filter_list = $_args->api_callback;
	}
}
?>