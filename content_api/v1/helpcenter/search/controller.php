<?php
namespace content_api\v1\helpcenter\search;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("helpcenter")->ALL("v1/helpcenter/search");
	}
}
?>