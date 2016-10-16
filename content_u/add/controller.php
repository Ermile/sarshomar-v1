<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route() {
		// check login
		parent::check_login();

		// list of poll of this user
		$this->get("knowledge", "knowledge")->ALL("knowledge");

		// add new
		$this->get(false, "add")->ALL("add");
		$this->post("add")->ALL("add");

		// for add survey
		$this->get("survey", "survey")->ALL("/^(.*)\/add$/");
		$this->post("add")->ALL("/^(.*)\/add$/");

		// add filter for survey or poll
		$this->get("filter", "filter")->ALL("/^(.*)\/filter$/");
		$this->post("filter")->ALL("/^(.*)\/filter$/");

		// publish survey or poll
		$this->get("publish", "publish")->ALL("/^(.*)\/publish$/");
		$this->post("publish")->ALL("/^(.*)\/publish$/");

		// delete poll
		// $this->get("delete", false)->ALL("/^delete\/(\d+)$/");
	}
}
?>