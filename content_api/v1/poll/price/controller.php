<?php
namespace content_api\v1\poll\price;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("price")->ALL("price/calc");
	}
}
?>