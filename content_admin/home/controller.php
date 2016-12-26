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
		else
		{
			// check permission
			$this->access('admin', 'admin', 'admin', 'block');
		}

	}
}
?>