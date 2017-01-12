<?php
namespace content_u\home;

class controller extends \mvc\controller
{

	/**
	 * route
	 */
	function _route()
	{
		$url = \lib\router::get_url();

		if(substr($url, 0, 1) == '$')
		{
			\lib\router::set_repository('content');
			\lib\router::set_controller("\\content\\knowledge\\controller");

			// \lib\router::set_controller("\\content_u\\knowledge\\controller");
			return;
		}

		// try sarshomar
		if(\lib\utility::get("inspection") == "inestimable" && !$this->login())
		{
			$this->redirector($this->url("base"). "/features/guest")->redirect();
			return ;
		}

		$this->check_login();

		$this->get(false, "profile")->ALL();

		$this->post("captcha")->ALL();
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
			// $this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			$this->view()->data->notlogin = true;
		}
	}
}
?>