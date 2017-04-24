<?php
namespace content_admin\attachments;

class view extends \mvc\view
{
	public function view_attachments($_args)
	{
		$this->data->attachment_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];
		// $this->data->download_url = "https://dl.sarshomar.com";
		$this->data->download_url = $this->url('base');
	}

	public function view_show($_args)
	{
		var_dump($_args);
		exit();
	}
}
?>