<?php
namespace content_api\v1\poll\search;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("search")->ALL("v1/poll/search");
	}
}
?>