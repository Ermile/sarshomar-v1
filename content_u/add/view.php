<?php
namespace content_u\add;

class view extends \mvc\view
{


	/**
	 * view users add
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_knowledge($o)
	{
		$this->data->datatable = $o->api_callback;
	}


	/**
	 * add add form and options
	 */
	function view_add()
	{
		$this->include->fontawesome = true;

		$this->data->form_add = true;
		$this->data->max_member = 100000;
		$this->data->min_member = 1;
		$this->data->cats = [
								['txt' => 'برنامه نویسان',	'value' => 'programing'],
								['txt' => 'دانشجویان',		'value' => 'student'],
								['txt' => 'کارمندان',		'value' => 'men'],
								['txt' => 'اموات و گذشتگان','value' => 'die'],
								['txt' => 'مرحوم مغفور',	'value' => 'diee'],
								['txt' => 'همه',			'value' => 'all']
							];

	}


	/**
	 * edit add
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_edit($o)
	{
		$this->data->form_edit = true;
		$this->data->post_id = $o->match->url[0][1];
		$this->data->form_data = $o->api_callback;
	}



	function view_filter($_args)
	{
		$this->data->poll_id = $_args->match->url[0][1];
		$this->data->filter_form = true;
		$this->data->add_filters = $_args->api_callback['add_filters'];
		$this->data->user_detail_filter = $_args->api_callback['user_detail_filter'];
	}
}
?>