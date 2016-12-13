<?php
namespace content_admin\transactionitems;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("add", "add")->ALL("transactionitems");
		$this->get("edit", "edit")->ALL("/^transactionitems\/(\d+)$/");
		$this->post("transactionitems")->ALL("transactionitems");
		$this->post("edit")->ALL("/^transactionitems\/(\d+)$/");
	}
}
?>