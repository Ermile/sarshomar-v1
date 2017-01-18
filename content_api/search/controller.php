<?php
namespace content_api\search;

trait controller
{
	public function route_search()
	{
		$this->get("search", false)->ALL("search");
		$this->post("search")->ALL("search");
	}
}
?>