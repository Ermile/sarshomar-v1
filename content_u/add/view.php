<?php
namespace content_u\add;

class view extends \mvc\view
{


	/**
	 * view users add
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function view_knowledge($_args)
	{
		$this->data->datatable = $_args->api_callback;
	}

	function view_publish($_args)
	{
		$this->data->publish_form = true;
	}

	/**
	 * add add form and options
	 */
	function view_add()
	{
		$this->include->fontawesome = true;
		$this->data->form_add = true;
	}


	/**
	 * ready to load survey
	 */
	function view_survey($_args)
	{
		// enable survey mod to load buttom and something else
		$this->data->survey_mod = true;
		$poll_list = \lib\db\survey::get_poll_list($this->model()->check_poll_url($_args));
		$this->data->poll_list = $poll_list;
	}

	/**
	 * edit add
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function view_edit($_args)
	{
		$this->data->form_edit = true;
		$this->data->post_id = $this->model()->check_poll_url($_args);
		$this->data->form_data = $_args->api_callback;
	}



	function view_filter($_args)
	{
		$poll_survey_id = $this->model()->check_poll_url($_args);
		if($poll_survey_id)
		{
			$this->data->poll_id = $poll_survey_id;

			$this->data->filter_form = true;
			$this->data->add_filters = $_args->api_callback['add_filters'];
			$this->data->user_detail_filter = $_args->api_callback['user_detail_filter'];
		}
	}
}
?>