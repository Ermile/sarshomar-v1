<?php
namespace content_u\question;

class view extends \mvc\view
{


	/**
	 * view users question
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_question($o)
	{
		$this->data->datatable = $o->api_callback;
	}


	/**
	 * add question form and options
	 */
	function view_question_add()
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
	 * edit question
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_question_edit($o)
	{
		$this->data->form_edit = true;
		$this->data->post_id = $o->match->url[0][1];
		$this->data->form_data = $o->api_callback;
	}



	function view_question_filter($_args)
	{
		$this->data->poll_id = $_args->match->url[0][1];
		$this->data->filter_form = true;
		$this->data->question_filters = $_args->api_callback['question_filters'];
		$this->data->user_detail_filter = $_args->api_callback['user_detail_filter'];
	}
}
?>