<?php
namespace content_u\token;

class controller extends  \content_u\home\controller
{
	public function _route()
	{

		parent::check_login();

		$this->get("token", "token")->ALL();
		$this->post("token")->ALL();
	}
}

?>