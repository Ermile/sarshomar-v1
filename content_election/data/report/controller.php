<?php
namespace content_election\data\report;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->access('election:data:admin', 'block');

		$this->get("list", "list")->ALL();

		$this->get("add", "add")->ALL("/data\/report\/(\d+)/");

		$this->post("add_result")->ALL("/data\/report\/(\d+)/");

		if(preg_match("/data\/report\/(\d+)/", \lib\router::get_url()))
		{
			$this->display_name = 'content_election\data\report\result.html';
		}

		$this->get("report", "report")->ALL("/data\/report\/edit=(\d+)/");

		$this->get("report_vote", "report_vote")->ALL("/data\/report\/vote=(\d+)/");

		$this->post("report_vote")->ALL("/data\/report\/vote=(\d+)/");

		if(preg_match("/data\/report\/vote=(\d+)/", \lib\router::get_url()))
		{
			$this->display_name = 'content_election\data\report\vote.html';
		}

		$this->post("edit")->ALL("/data\/report\/edit=(\d+)/");

		$this->post('report')->ALL();
	}
}
?>