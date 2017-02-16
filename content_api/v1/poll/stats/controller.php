<?php
namespace content_api\v1\poll\stats;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("stats")->ALL("v1/poll/stats");
	}
}
?>