<?php
namespace content_u\me;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("me", "me")->ALL();
		$this->post("me")->ALL();
	}
}

?>