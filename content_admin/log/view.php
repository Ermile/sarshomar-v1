<?php
namespace content_admin\log;

class view extends \mvc\view
{
	public function view_log($_args)
	{
		$this->data->log_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];
	}
}
?>