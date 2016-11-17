<?php
namespace content_u\delete;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("delete", "delete")->ALL();
		$this->post("delete")->ALL();
	}
}

?>