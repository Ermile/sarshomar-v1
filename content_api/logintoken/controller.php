<?php
namespace content_api\logintoken;

trait controller
{
	public function route_logintoken()
	{
		$this->get("token", false)->ALL("getLogintoken");
		$this->post("token")->ALL("getLogintoken");

		$this->get("guest_token", false)->ALL("getGuesttoken");
		$this->post("guest_token")->ALL("getGuesttoken");
	}
}
?>