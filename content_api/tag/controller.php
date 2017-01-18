<?php
namespace content_api\tag;

trait controller
{
	public function route_tag()
	{
		$this->get("tag", false)->ALL("tag");
		$this->post("tag")->ALL("tag");
	}
}
?>