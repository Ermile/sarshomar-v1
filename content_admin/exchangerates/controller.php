<?php
namespace content_admin\exchangerates;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("add", "add")->ALL("exchangerates");
		$this->get("edit", "edit")->ALL("/^exchangerates\/(\d+)$/");
		$this->post("add")->ALL("exchangerates");
		$this->post("edit")->ALL("/^exchangerates\/(\d+)$/");
	}
}
?>