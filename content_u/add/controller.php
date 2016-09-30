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

		// list of query
		$this->get("knowledge", "knowledge")->ALL("knowledge");

		// add new add
		$this->get(false, "add")->ALL("add");
		$this->post("add")->ALL("add");

		// edit add
		$this->get("edit", "edit")->ALL("/^edit\=(\d+)$/");
		$this->post("edit")->ALL("/^edit\=(\d+)$/");

		// delete add
		$this->get("delete", false)->ALL("/^delete\/(\d+)$/");


		// add new add
		$this->get("filter", "filter")->ALL("/^filter\=(\d+)$/");
		$this->post("filter")->ALL("/^filter\=(\d+)$/");

	}
}
?>