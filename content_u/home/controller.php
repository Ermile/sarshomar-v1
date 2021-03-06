<?php
namespace content_u\home;

class controller extends \content_u\main\controller
{

	/**
	 * route
	 */
	function _route()
	{
		parent::_route();

		$this->route("billing/charge");

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
			$this->redirector($this->url("base"). "/benefits/guest")->redirect();
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
		// // check logined
		// if(!$this->login())
		// {
		// 	$url = \lib\utility\safe::safe($_SERVER['REQUEST_URI']);
		// 	$this->redirector()->set_domain()->set_url('enter?referer='. $url)->redirect();
		// 	// $this->view()->data->notlogin = true;
		// }
	}
}
?>