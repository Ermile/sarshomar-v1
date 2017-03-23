<?php
namespace content_u\token;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("token", "token")->ALL();
		$this->post("token")->ALL();
	}
}

?>