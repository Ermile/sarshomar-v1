<?php
namespace content_admin\attachments;

class view extends \mvc\view
{
	public function view_attachments($_args)
	{
		$this->data->attachment_list = $_args->api_callback;
		$ids = null;
		if(is_array($_args->api_callback))
		{
			$ids = array_column($_args->api_callback, 'id');
			$ids = json_encode($ids, JSON_UNESCAPED_UNICODE);
		}
		$this->data->ids = $ids;
		$this->data->search_value =  $_args->get("search")[0];
		// $this->data->download_url = "https://dl.sarshomar.com";
	}

	public function view_show($_args)
	{
		var_dump($_args);
		exit();
	}
}
?>