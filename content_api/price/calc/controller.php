<?php
namespace content_api\price\calc;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("price")->ALL("price/calc");
	}
}
?>