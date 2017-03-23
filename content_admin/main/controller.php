<?php
namespace content_admin\main;

class controller extends \mvc\controller
{
	function _route()
	{
		parent::_route();
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
			$url = \lib\utility\safe::safe($_SERVER['REQUEST_URI']);
			$this->redirector()->set_domain()->set_url('enter?referer='. $url)->redirect();
		}
		else
		{
			// check permission
			$this->access('admin', 'admin', 'admin', 'block');
		}

	}
}
?>