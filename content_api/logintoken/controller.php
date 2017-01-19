<?php
namespace content_api\logintoken;

trait controller
{
	public function route_logintoken()
	{
		$this->get("token", false)->ALL("loginToken");
		$this->post("token")->ALL("loginToken");

		$this->get("guest_token", false)->ALL("guestToken");
		$this->post("guest_token")->ALL("guestToken");
	}
}
?>