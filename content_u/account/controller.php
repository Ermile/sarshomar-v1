<?php
namespace content_u\account;

class controller extends \mvc\controller
{
	public function _route()
	{
		$this->get("account", "account")->ALL();
		$this->post("account")->ALL();
	}
}

?>