<?php
namespace content_api\logintoken;

trait controller
{
	public function route_logintoken()
	{
		$this->get("login_token", false)->ALL("loginToken");
		$this->post("login_token")->ALL("loginToken");

	}
}
?>