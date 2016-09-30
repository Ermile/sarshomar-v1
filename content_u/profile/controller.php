<?php
namespace content_u\profile;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("profile", "profile")->ALL();
		$this->post("profile")->ALL();
	}
}

?>