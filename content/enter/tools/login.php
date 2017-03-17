<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait login
{
	/**
	 * login
	 */
	public function login_set()
	{
		$myfields =
		[
			'id',
			'user_displayname',
			'user_mobile',
			'user_meta',
			'user_status',
		];

		$this->setLoginSession($this->user_data, $myfields);
		$this->login_remember();
		debug::msg('direct', true);
		if(isset($_SESSION['first_signup']) && $_SESSION['first_signup'] === true)
		{
			$this->redirector('ask')->redirect();
		}
		else
		{
			$this->redirector('@')->redirect();
		}
	}


	/**
	 * set remeber me
	 */
	public function login_remember()
	{
		if(\lib\utility::cookie('remember_me'))
		{
			\lib\db\options::delete([
			'option_cat'	=> 'session',
			'option_key'	=> 'rememberme',
			'option_value'	=> \lib\utility::cookie('remember_me'),
			]);
		}

		$uniq_id = urlencode(\lib\utility::hasher(time() . $this->user_id)) . rand(701, 1301);
		$insert = \lib\db\options::insert([
			'user_id' 		=> $this->user_id,
			'option_cat'	=> 'session',
			'option_key'	=> 'rememberme',
			'option_value'	=> $uniq_id,
			'date_modified'	=> date("Y-m-d H:i:s", time())
			]);
		$service_name = '.' . \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
		$tld = \lib\router::get_domain(-1);
		$service_name .= '.' . end($tld);
		setcookie("remember_me", $uniq_id, time() + (60*60*24*365), '/', $service_name);
	}



	/**
	 * referer
	 *
	 * @param      array  $_args  The arguments
	 */
	public function login_referer($_args = [])
	{
		\lib\debug::msg('direct', true);
		$url = $this->url("root");
		if(\lib\router::$prefix_base)
		{
			$url .= '/'.\lib\router::$prefix_base;
		}

		if(\lib\utility::get('referer'))
		{
			$url .= '/referer?to=' . \lib\utility::get('referer');
			$this->redirector($url)->redirect();
		}
		elseif(\lib\utility\option::get('account', 'status'))
		{
			$url = $this->url("root");
			$_redirect_sub = \lib\utility\option::get('account', 'meta', 'redirect');

			if($_redirect_sub !== 'home')
			{
				// if(\lib\utility\option::get('config', 'meta', 'fakeSub'))
				// {
				// 	echo $this->redirector()->set_subdomain()->set_url($_redirect_sub)->redirect();
				// }
				// else
				// {
				//
				// }

				$url .= '/'. $_redirect_sub;

				if(isset($_args['user_id']) && $_args['user_id'])
				{
					$user_language = \lib\db\users::get_language($_args['user_id']);
					if($user_language && \lib\utility\location\languages::check($user_language))
					{
						$url .= \lib\define::get_current_language_string($user_language);
					}

				}
				$this->redirector($url)->redirect();
			}
		}
		$this->redirector()->set_domain()->set_url()->redirect();
	}



	/**
	 * login whit remember
	 */
	public function login_by_remember()
	{
		if(\lib\utility::cookie('remember_me') && !$this->login())
		{
			$get = \lib\db\options::get([
			'option_cat'	=> 'session',
			'option_key'	=> 'rememberme',
			'option_status'	=> 'enable',
			'option_value'	=> \lib\utility::cookie('remember_me'),
			'limit'			=> 1
			]);
			if($get && isset($get['user_id']))
			{
				$this->user_id   = $get['user_id'];
				$this->user_data = \lib\db\users::get($get['user_id']);
				$this->login_set();
				return true;
			}
		}
		return false;
	}
}
?>