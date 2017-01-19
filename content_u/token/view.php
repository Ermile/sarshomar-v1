<?php
namespace content_u\token;

class view extends \mvc\view
{
	public function view_token($_args)
	{
		$this->data->api_key = $_args->api_callback;
	}
}
?>