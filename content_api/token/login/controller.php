<?php
namespace content_api\token\login;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("login_token")->ALL("token/login");
	}
}
?>