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
	 * edit add
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function view_edit($_args)
	{
		$this->data->form_edit = true;
		$this->data->post_id = $_args->match->url[0][1];
		$this->data->form_data = $_args->api_callback;
	}



	function view_filter($_args)
	{
		if(isset($_args->match->url[0][1]))
		{
			$this->data->poll_id = $_args->match->url[0][1];

			$this->data->filter_form = true;
			$this->data->add_filters = $_args->api_callback['add_filters'];
			$this->data->user_detail_filter = $_args->api_callback['user_detail_filter'];
		}
	}
}
?>