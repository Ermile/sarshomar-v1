<?php
namespace content_u\home;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		if(substr(\lib\router::get_url(),0,1) == '$')
		{
			\lib\router::set_controller("\\content_u\\knowledge\\controller");
			return ;
		}
		/**
		 * route url like 4dgF/add to add folder
		 */
		if(preg_match("/^(.*)\/(add|filter|publish)$/", \lib\router::get_url(), $controller_name))
		{
			\lib\router::set_controller("\\content_u\\$controller_name[2]\\controller");
			return ;
		}

		// try sarshomar
		if(\lib\utility::get("inspection") == "inestimable" && !$this->login())
		{
			$signup_inspection = \lib\db\users::signup_inspection();
			if($signup_inspection)
			{
				\lib\db\users::set_login_session(null, null, $signup_inspection);
			}
		}

		$this->check_login();
		$this->get(false, "profile")->ALL();
	}


	/**
	 * check users login
	 * if not login redirect to login page
	 */
	public function check_login()
	{
		// check logined
		if(!$this->login())
		{
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
		}

	}
}
?>