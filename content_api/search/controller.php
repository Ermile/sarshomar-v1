<?php
namespace content_api\search;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("search")->ALL("search");
	}
}
?>