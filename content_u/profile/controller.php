<?php
namespace content_u\profile;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("profile", "profile")->ALL();
		$this->post("profile")->ALL();
	}
}

?>