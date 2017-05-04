<?php
namespace content_election\home;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		$url = \lib\router::get_url();
		if($id = $this->model()->check_url($url))
		{
			$this->get("load", "load")->ALL("/.*\/($id)/");
		}
	}
}
?>