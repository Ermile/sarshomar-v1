<?php
namespace content_election\data\result;

class view extends \content_election\main\view
{
	public function view_result($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;
	}


}
?>