<?php
namespace content_admin\home;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		$this->check_login();

		if(substr(\lib\router::get_url(),0,1) == '$')
		{
			\lib\router::set_controller("\\content_admin\\knowledge\\controller");
			return ;
		}
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