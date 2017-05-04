<?php
namespace content_election\data\report;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		$this->access('election', 'data', 'admin', 'block');

		$this->get("list", "list")->ALL();
		$this->get("add", "add")->ALL("/data\/report\/add\/(\d+)/");

		$this->post("add_result")->ALL("/data\/report\/add\/(\d+)/");
		if(preg_match("/data\/report\/add\/(\d+)/", \lib\router::get_url()))
		{
			$this->display_name = 'content_election\data\report\result.html';
		}

		$this->get("report", "report")->ALL("/data\/report\/edit=(\d+)/");
		$this->post("edit")->ALL("/data\/report\/edit\/(\d+)/");
		$this->post('report')->ALL();
	}
}
?>