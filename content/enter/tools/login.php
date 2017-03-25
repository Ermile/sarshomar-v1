<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait login
{
	/**
	 * find redirect url
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function find_redirect_url($_url = null)
	{
		if($_url)
		{
			return $_url;
		}

		$url = \lib\define::get_current_language_string();
		if(utility::get('referer'))
		{
			$url = utility::get('referer');
		}
		elseif(isset($_SESSION['first_signup']) && $_SESSION['first_signup'] === true)
		{
			$url .= '/ask';
		}
		else
		{
			$url = null;
			$user_language = \lib\db\users::get_language($this->user_id);
			if($user_language && \lib\utility\location\languages::check($user_language))
			{
				$url .= \lib\define::get_current_language_string($user_language);
			}
			else
			{
				$url .= \lib\define::get_current_language_string();
			}

			$url .= '/@';
		}

		return $url;
	}


	/**
	 * sync guest data to new login
	 */
	public function sync_guest()
	{
		$old_user_id = $this->login('id');
		$new_user_id = $this->user_id;

		if(intval($old_user_id) === intval($new_user_id))
		{
			\lib\db\logs::set('enter:guest:userid:is:guestid', $this->user_id, $log_meta);
			return;
		}

		\lib\utility\sync::web_guest($new_user_id, $old_user_id);
	}


	/**
	 * the gues has login
	 * logout the guest
	 * sync guset by new user
	 * login new user
	 *
	 * @param      <type>  $_url   The url
	 */
	public function login_set_guest($_url = null)
	{
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'mobile'  => $this->mobile,
				'user_id' => $this->user_id,
				'input'   => utility::post(),
				'session' => $_SESSION,
			]
		];

		$user_status = \lib\utility\users::get_status($this->user_id);
		switch ($user_status)
		{
			case 'active':
				\lib\db\logs::set('enter:guest:have:active:user', $this->user_id, $log_meta);
				break;
			case 'awaiting':
				$this->sync_guest();
				break;

			default:
				\lib\db\logs::set('enter:guest:invalid:status', $this->user_id, $log_meta);
				break;
		}
		// distroy guest session to set new session
		$this->put_logout();
		if(\lib\utility::cookie('remember_me'))
		{
			\lib\db\options::delete([
			'option_cat'	=> 'session',
			'option_key'	=> 'rememberme',
			'option_value'	=> \lib\utility::cookie('remember_me'),
			]);
		}
	}


	/**
	 * login
	 */
	public function login_set($_url = null)
	{
		if($this->is_guest)
		{
			$this->login_set_guest();
		}

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
		if(isset($_SESSION['first_signup']) && $_SESSION['first_signup'] === true)
		{
			$args =
			[
				'mobile'   => $this->mobile,
				'ref'      => null,
				'type'     => null,
				'port'     => 'site',
				'subport'  => null,
				'user_id'  => $this->user_id,
				'language' => \lib\define::get_language(),
			];
			\lib\utility\users::verify($args);
		}

		$user_verify =
		[
			'mobile'   => $this->mobile,
			'ref'      => null,
			'port'     => 'site',
			'subport'  => null,
			'user_id'  => $this->user_id,
			'language' => \lib\define::get_language(),
		];

		\lib\utility\users::verify($user_verify);


		debug::msg('direct', true);
		$this->redirector($this->find_redirect_url($_url))->redirect();
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
	public function login_by_remember($_url = null)
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
				$this->user_data = \lib\utility\users::get($get['user_id']);
				$this->mobile    = \lib\utility\users::get_mobile($get['user_id']);
				$this->login_set($_url);
				return true;
			}
		}
		return false;
	}
}
?>