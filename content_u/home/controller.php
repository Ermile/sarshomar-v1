<?php
namespace content_u\home;

class controller extends \mvc\controller
{
	function _route() {
		$this->check_login();
		$this->get("profile", "profile")->ALL();
	}

	function check_login(){
		// check logined
		if(!$this->login()){
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

	}
}
?>