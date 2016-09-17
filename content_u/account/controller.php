<?php
namespace content_u\account;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("account", "account")->ALL();
		$this->post("account")->ALL();
	}
}

?>