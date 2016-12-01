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
			\lib\router::set_controller("\\content_u\\knowledge\\controller");
			return;
		}

		if(preg_match("/^(.*)\/(add|filter|publish)$/", $url, $controller_name))
		{
			if(isset($controller_name[2]))
			{
				\lib\router::set_controller("\\content_u\\$controller_name[2]\\controller");
				return ;
			}
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
			// $this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			$this->view()->data->notlogin = true;
		}
	}


	/**
	 * creat top page progress url link of progress add questin
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public function page_progress_url($_poll_id, $_status)
	{
		$this->view()->data->progress_status = $_status;
		if($_poll_id)
		{
			$default_url = $this->url('base') . '/@/add/' . \lib\utility\shortURL::encode($_poll_id);
			$this->view()->data->add_url     = $default_url;
			$this->view()->data->filter_url  = $default_url . '/filter';
			$this->view()->data->publish_url = $default_url . '/publish';
		}
	}
}
?>