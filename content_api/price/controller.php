<?php
namespace content_api\price;

trait controller
{
	public function route_price()
	{
		$this->get("price", false)->ALL("calcPrice");
		$this->post("price")->ALL("calcPrice");
	}
}
?>