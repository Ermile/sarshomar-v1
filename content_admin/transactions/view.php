<?php
namespace content_admin\transactions;

class view extends \mvc\view
{
	public function view_transactions($_args)
	{
		$this->data->transaction_list = $_args->api_callback;
		$this->data->search_value =  $_args->get("search")[0];
	}
}
?>