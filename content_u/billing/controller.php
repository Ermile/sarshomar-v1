<?php
namespace content_u\billing;

class controller extends  \content_u\home\controller
{
	public function _route()
	{

		parent::check_login();

		$this->get("billing", "billing")->ALL();
		$this->post("billing")->ALL();
		$this->get("verify", "verify")->ALL("/billing\/verify\/(zarinpal)/");
	}
}

?>