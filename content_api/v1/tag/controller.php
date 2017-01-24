<?php
namespace content_api\v1\tag;

class controller extends  \content_api\v1\home\controller
{
	public function route_tag()
	{
		$this->get("tag")->ALL("tag");
		$this->post("tag")->ALL("tag");
	}
}
?>