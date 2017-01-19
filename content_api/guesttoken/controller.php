<?php
namespace content_api\guesttoken;

trait controller
{
	public function route_guesttoken()
	{
		$this->get("guest_token", false)->ALL("guestToken");
		$this->post("guest_token")->ALL("guestToken");
	}
}
?>