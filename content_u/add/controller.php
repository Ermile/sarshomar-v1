<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route() {

		// check login
		parent::check_login();

		if(preg_match("/(filter|publish)$/", \lib\router::get_url(), $load))
		{
			\lib\router::set_controller("\\content_u\\$load[0]\\controller");
			return;
		}

		// $this->post("search")->ALL("/add\/search/");

		// add new
		$this->get(false, "add")->ALL("/^add$/");
		$this->post("add")->ALL("/^add$/");

		// for add survey
		$this->get("survey", "survey")->ALL("/^add\/(.*)$/");
		$this->post("add")->ALL("/^add\/(.*)$/");


	}
}
?>