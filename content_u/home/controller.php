<?php
namespace content_u\home;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		/**
		 * route url like 4dgF/add to add folder
		 */
		if(preg_match("/^(.*)\/(add|filter|publish)$/", \lib\router::get_url()))
		{
			\lib\router::set_controller("\\content_u\\add\\controller");
			return ;
		}

		if(\lib\utility::get("inspection") == "inestimable" && !$this->login())
		{
			\lib\db\users::signup_inspection();
		}

		$this->check_login();
		$this->get("profile", "profile")->ALL();
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
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
		}

	}
}
?>