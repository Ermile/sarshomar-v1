<?php
namespace content_admin\transactionitems;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("add", "add")->ALL("transactionitems");
		$this->get("edit", "edit")->ALL("/^transactionitems\/(\d+)$/");
		$this->post("add")->ALL("transactionitems");
		$this->post("edit")->ALL("/^transactionitems\/(\d+)$/");
	}
}
?>