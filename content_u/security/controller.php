<?php
namespace content_u\security;

class controller extends  \content_u\home\controller
{
	public function _route()
	{

		parent::check_login();

		$this->get("security", "security")->ALL();
		$this->post("security")->ALL();
	}
}

?>