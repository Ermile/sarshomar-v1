<?php
namespace content_admin\logs;

class view extends \content_admin\main\view
{
	public function view_logs($_args)
	{
		$this->data->log_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];

		$fields = [
		'desc',
		'id',
		'logitem_id',
		'type',
		'caller',
		'title',
		'priority',
		'user_id',
		'data',
		'meta',
		'status',
		'createdate',
		'date_modified',
		];
		$this->order_url($_args, $fields);
	}
}
?>