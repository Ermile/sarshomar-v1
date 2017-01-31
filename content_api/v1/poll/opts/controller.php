<?php
namespace content_api\v1\poll\opts;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("opts")->ALL("v1/poll/opts");
	}
}
?>