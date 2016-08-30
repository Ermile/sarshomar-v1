<?php
namespace content_u\polls;

class view extends \mvc\view
{
	function view_question($o) {
		$this->data->datatable = $o->api_callback;
	}

	function view_polls($o) {
		var_dump($o->api_callback);
	}

	function view_show($o) {
		$this->data->datatable = $o->api_callback;
	}
}
?>