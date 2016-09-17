<?php
namespace content_u\account;

class view extends \mvc\view
{
	public function view_account($_args)
	{
		$this->data->account = $_args->api_callback;
	}
}
?>