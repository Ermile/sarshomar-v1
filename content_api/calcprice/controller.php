<?php
namespace content_api\calcprice;

trait controller
{
	public function route_calcprice()
	{
		$this->get("price", false)->ALL("calcPrice");
		$this->post("price")->ALL("calcPrice");
	}
}
?>