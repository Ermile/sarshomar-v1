<?php
namespace mvc;

class controller extends \lib\mvc\controller
{

	public function _route()
	{
		if(!$this->login())
		{
			$cookie = \lib\utility::cookie('remember_me');
			if($cookie)
			{
				$this->model()->mvc_login_by_remember();
			}
		}
	}


	/**
	 * the short url code to use in route of some page
	 *
	 * @var        string
	 */
	public static $shortURL = \lib\utility\shortURL::ALPHABET;
	public static $accept_poll_status = ['publish','stop','pause'];

	/**
	 * [handle_login_url description]
	 * @param  [type] $_module [description]
	 * @param  [type] $_param  [description]
	 * @param  [type] $_domain [description]
	 * @return [type]          [description]
	 */
	public function handle_account_url($_module, $_param, $_domain)
	{
		if(!$_param)
		{
			$_param = '?from='.$_module;
		}
		else
		{
			$_param .= '&from='.$_module;
		}

		switch ($_module)
		{
			// login
			case 'signin':
			case 'login':
			// signup
			case 'signup':
			case 'register':
			// recovery
			case 'recovery':
				$this->redirector()->set_domain($_domain)->set_url('enter'.$_param)->redirect();
				break;

			case 'signout':
			case 'logout':
				// $this->redirector()->set_domain()->set_url('logout'.$_param)->redirect();
				$this->redirector()->set_domain($_domain)->set_url(MyAccount. '/logout'.$_param)->redirect();
				break;
		}
	}
}
?>