<?php
namespace content_u\add;

class view extends \mvc\view
{


	/**
	 * view list of polls of this user
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function view_knowledge($_args)
	{
		// get list of poll of this users (this users creat it)
		$this->data->datatable = $_args->api_callback;
	}


	/**
	 * ready to load add poll
	 */
	function view_add()
	{
		$this->include->fontawesome = true;
		$this->data->form_add       = true;
	}


	/**
	 * ready to edit poll
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function view_edit($_args)
	{
		// enable form_edit to load html of edit
		$this->data->form_edit = true;
		// get the poll id from url
		$this->data->post_id   = $this->model()->check_poll_url($_args);
		// get existing data of this poll to load in page and user can edit it
		$this->data->form_data = $_args->api_callback;
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

		// enable filter_form to load html
		$this->data->filter_form = true;
		// save poll_id to form
		$this->data->poll_id = $poll_survey_id;
		// get existing filter list to load in html and user can select this
		$this->data->filter_list = $_args->api_callback;
	}


	/**
	 * ready to load publish page
	 * get the cat and get the article
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_publish($_args)
	{
		// load publish html form
		$this->data->publish_form = true;
		// set the short url to data
		$this->data->short_url = $_args->api_callback;
		// get all cat_poll from terms
		$this->data->cat = \lib\db\cats::get("cat_poll");
		// get article
		$args =	['post_type' => 'article'];
		$this->data->article = \lib\db\polls::xget($args);
	}
}
?>