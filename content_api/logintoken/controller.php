<?php
namespace content_api\logintoken;

class controller extends  \mvc\controller
{
	public function _route()
	{
		$this->get("token", false)->ALL("getLogintoken");
		$this->post("token")->ALL("getLogintoken");

		$this->get("guest_token", false)->ALL("getGuesttoken");
		$this->post("guest_token")->ALL("getGuesttoken");
	}
}
?>