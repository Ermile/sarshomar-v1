<?php
namespace content_election\admin\election;

class controller extends \mvc\controller
{
	public function _route()
	{
		$this->get("list", "list")->ALL();
		$this->get("election", "election")->ALL("/admin\/election\/edit=(\d+)/");
		$this->post("edit")->ALL("/admin\/election\/edit=(\d+)/");
		$this->post('election')->ALL();
	}
}
?>