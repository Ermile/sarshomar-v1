<?php
namespace content_api\budget;

trait controller
{
	public function route_budget()
	{
		$this->get("budget", false)->ALL("budget");
		$this->post("budget")->ALL("budget");
	}
}
?>