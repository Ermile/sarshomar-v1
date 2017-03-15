<?php
namespace content_admin\log;

class view extends \mvc\view
{
	public function view_log($_args)
	{
		$this->data->log = $_args->api_callback;
	}
}
?>