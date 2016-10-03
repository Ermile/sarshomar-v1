<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route() {
		parent::check_login();
		// check logined
		if(!$this->login()){
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

		// list of poll
		$this->get("knowledge", "knowledge")->ALL("knowledge");
		// add new
		$this->get(false, "add")->ALL("add");
		$this->post("add")->ALL("add");

		// for add survey
		$this->get("set_survey", "add")->ALL("/^(.*)\/add$/");


		// add filter
		$this->get("filter", "filter")->ALL("/^(.*)\/filter$/");
		$this->post("filter")->ALL("/^(.*)\/filter$/");

		// publish
		$this->get("publish", "publish")->ALL("/^(.*)\/publish$/");
		$this->post("publish")->ALL("/^(.*)\/publish$/");


		// delete poll
		$this->get("delete", false)->ALL("/^delete\/(\d+)$/");
	}
}
?>