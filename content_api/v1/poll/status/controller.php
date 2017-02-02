<?php
namespace content_api\v1\poll\status;

class controller extends  \content_api\v1\home\controller
{
	/**
	 * poll status
	 */
	public function _route()
	{
		$this->get("status")->ALL("v1/poll/status");
		$this->put("status")->ALL("v1/poll/status");
	}
}
?>