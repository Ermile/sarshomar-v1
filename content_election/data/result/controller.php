<?php
namespace content_election\data\result;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		$this->get("result", "result")->ALL("/data\/result\/(\d+)/");
	}
}
?>