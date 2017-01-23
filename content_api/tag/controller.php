<?php
namespace content_api\tag;

class controller extends  \content_api\home\controller
{
	public function route_tag()
	{
		$this->get("tag")->ALL("tag");
		$this->post("tag")->ALL("tag");
	}
}
?>