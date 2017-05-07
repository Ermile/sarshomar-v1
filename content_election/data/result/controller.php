<?php
namespace content_election\data\result;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->access('election', 'data', 'admin', 'block');

		$this->get("result", "result")->ALL("/^data\/result\/(\d+)$/");

		$this->get("add_city", "add_city")->ALL("/data\/result\/(\d+)\/place/");
		if(preg_match("/data\/result\/(\d+)\/place/", \lib\router::get_url()))
		{
			$this->display_name = 'content_election\data\result\city_result.html';
		}
		$this->post("save_city")->ALL("/data\/result\/(\d+)\/place/");

		$this->get("election_list", "election_list")->ALL();

	}
}
?>