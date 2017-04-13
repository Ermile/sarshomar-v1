<?php
namespace content_admin\ranks;

class view extends \content_admin\main\view
{
	public function view_ranks($_args)
	{
		$this->data->log_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];
		$fields = [
			'member',
			'public',
			'filter',
			'ad',
			'money',
			'report',
			'vote',
			'like',
			'fav',
			'skip',
			'comment',
			'view',
			'other',
			'sarshomar',
			'createdate',
			'ago',
			'admin',
			'vip',
			'value',
			'url',
			'title',
		];
		$this->order_url($_args, $fields);
	}
}
?>