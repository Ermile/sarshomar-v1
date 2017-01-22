<?php
namespace content_api\temptoken;

trait controller
{
	public function route_temptoken()
	{
		$this->get("temp_token", false)->ALL("tempToken");
		$this->post("temp_token")->ALL("tempToken");

	}
}
?>