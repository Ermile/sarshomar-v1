<?php
namespace content_api\v1\tag\search;

class controller extends  \content_api\v1\home\controller
{
	public function route_tag()
	{
		$this->get("tag")->ALL("v1/tag");
	}
}
?>