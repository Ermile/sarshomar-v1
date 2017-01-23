<?php
namespace content_api\price\budget;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("budget")->ALL("price/budget");
	}
}
?>