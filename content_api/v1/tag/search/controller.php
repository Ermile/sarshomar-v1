<?php
namespace content_api\v1\tag\search;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("tag")->ALL("v1/tag/search");
	}
}
?>