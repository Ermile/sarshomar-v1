<?php
namespace content_admin\units;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("add", "add")->ALL("units");
		$this->get("edit", "edit")->ALL("/^units\/(\d+)$/");
		$this->post("add")->ALL("units");
		$this->post("edit")->ALL("/^units\/(\d+)$/");
	}
}
?>