<?php
namespace content_u\billing;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("billing", "billing")->ALL();
		$this->post("billing")->ALL();
		$this->get("verify")->ALL("/billing\/verify\/(zarinpal)/");
	}
}

?>