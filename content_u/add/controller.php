<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route() {
		// check login
		parent::check_login();

		// add new
		$this->get(false, "add")->ALL("add");
		$this->post("add")->ALL("add");

		// for add survey
		$this->get("survey", "survey")->ALL("/^(.*)\/add$/");
		$this->post("add")->ALL("/^(.*)\/add$/");
	}
}
?>