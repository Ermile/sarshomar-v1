<?php
namespace content_election\admin\candida;

class controller extends \mvc\controller
{
	public function _route()
	{
		$this->get("list", "list")->ALL();
		$this->get("candida", "candida")->ALL("/admin\/candida\/edit=(\d+)/");
		$this->post("edit")->ALL("/admin\/candida\/edit=(\d+)/");
		$this->post('candida')->ALL();
	}
}
?>