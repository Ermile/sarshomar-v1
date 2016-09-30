<?php
namespace content_u\knowledge;

class view extends \mvc\view
{
	public function view_list($_args)
	{
		$this->data->poll_list = $_args->api_callback;
	}
}
?>