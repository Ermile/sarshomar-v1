<?php
namespace content_api\poll\search;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("search")->ALL("poll/search");
	}
}
?>