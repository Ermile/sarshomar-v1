<?php
namespace content_admin\userdashboards;

class view extends \content_admin\main\view
{
	public function view_userdashboards($_args)
	{
		$this->data->userdashboards_list = $_args->api_callback;

		$this->data->search_value =  $_args->get("search")[0];
		$fields = [
		'id',
		'user_mobile',
		'user_username',
		'user_displayname',
		'user_status',
		'budget',
		'user_validstatus',
		'user_port',
		'user_trust',
		'user_verify',
		'user_language',
		'poll_answered',
		'poll_skipped',
		'my_poll',
		'my_poll_answered',
		'my_poll_skipped',
		'user_referred',
		'user_verified',
		'comment_count',
		'draft_count',
		'publish_count',
		'awaiting_count',
		'my_fav',
		'my_like',
		];
		$this->order_url($_args, $fields);
	}
}
?>