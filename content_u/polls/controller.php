<?php
namespace content_u\polls;

class controller extends \content_u\home\controller
{
	function _route()
	{
		parent::check_login();
		// check logined
		if(!$this->login()){
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

		// show polls
		$this->get("show", "show")->ALL("polls");
		$this->post("save_answer")->ALL("polls");

		$this->get("polls", "polls")->ALL("/^polls\/\d+$/");
		$this->post("polls")->ALL("/^polls\/\d+$/");
	}
}
?>