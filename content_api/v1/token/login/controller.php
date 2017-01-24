<?php
namespace content_api\v1\token\login;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("login_token")->ALL("v1/token/login");
	}
}
?>