<?php
namespace content_election\home;

class view extends \content_election\main\view
{
	public function view_load($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;
	}
}
?>