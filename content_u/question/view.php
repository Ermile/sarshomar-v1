<?php
namespace content_u\question;

class view extends \mvc\view
{
	function view_question($o) {
		$this->data->datatable = $o->api_callback;
	}

	function view_question_add() {

	}

	function view_question_edit() {

	}
}
?>