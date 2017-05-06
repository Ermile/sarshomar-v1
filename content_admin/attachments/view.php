<?php
namespace content_admin\attachments;

class view extends \content_admin\main\view
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

	public function view_view($_args)
	{
		$this->data->result = $_args->api_callback;
		$id = (isset($_args->match->url[0][1])) ? $_args->match->url[0][1] : null;
		if(!$id || !is_numeric($id))
		{
			return false;
		}
		$get_post = \lib\db\posts::get_one($id);

		if(isset($get_post['post_meta']) && is_array($get_post['post_meta']))
		{
			$this->data->current_file = $get_post['post_meta'];
		}
	}
}
?>