<?php
namespace content_u\lists;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("list", "list")->ALL();
		$this->post("list")->ALL();
	}
}

?>