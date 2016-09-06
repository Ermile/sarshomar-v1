<?php
namespace content_u\question;

class view extends \mvc\view
{
	function view_question($o) {
		$this->data->datatable = $o->api_callback;
	}

	function view_question_add() {
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

	function view_question_edit($o) {
		$this->data->form_edit = true;
		$this->data->post_id = $o->match->url[0][1];
		$this->data->form_data = $o->api_callback;
	}
}
?>