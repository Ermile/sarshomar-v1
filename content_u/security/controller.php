<?php
namespace content_u\security;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("security", "security")->ALL();
		$this->post("security")->ALL();
	}
}

?>