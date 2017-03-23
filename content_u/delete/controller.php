<?php
namespace content_u\delete;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("delete", "delete")->ALL();
		$this->post("delete")->ALL();
	}
}

?>