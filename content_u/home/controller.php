<?php
namespace content_u\home;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		$this->check_login();
		$this->get("profile", "profile")->ALL();

		if(preg_match("/^(.*)\/filter$/", \lib\router::get_url()))
		{
			\lib\router::set_controller("\\content_u\\add\\controller");
			return ;
		}

	}


	/**
	 * check users login
	 * if not login redirect to login page
	 */
	function check_login()
	{
		// check logined
		if(!$this->login())
		{
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

	}
}
?>