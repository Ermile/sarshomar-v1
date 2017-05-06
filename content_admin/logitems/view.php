<?php
namespace content_admin\logitems;

class view extends \content_admin\main\view
{
	public function view_logitems($_args)
	{
		$this->data->log_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];
	}
}
?>