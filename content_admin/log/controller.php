<?php
namespace content_admin\log;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("log", "log")->ALL();
	}
}

?>