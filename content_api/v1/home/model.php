<?php
namespace content_api\v1\home;
use \lib\utility\permission;
use \lib\utility;
use \lib\debug;
use \lib\utility\token;

class model extends \mvc\model
{

	/**
	 * the user id
	 *
	 * @var        integer
	 */
	public $user_id = null;


	/**
	 * make debug return
	 * default is true
	 * in some where in site this method is false
	 *
	 * @var        boolean
	 */
	public $debug = true;


	/**
	 * the url
	 *
	 * @var        <type>
	 */
	public $url = null;


	/**
	 * the authorization
	 *
	 * @var        <type>
	 */
	public $authorization          = null;


	/**
	 * the parent api key
	 *
	 * @var        <type>
	 */
	public $parent_api_key         = null;
	public $parent_api_key_user_id = 0;


	use tools\get_token;
	use tools\ready;

	/**
	 * set permission of this user
	 */
	public function permission()
	{
		if($this->user_id)
		{
			$this->set_api_permission($this->user_id);
		}
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_name  The name
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $parm   The parameter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function _call($_name, $_args, $parm)
	{
		$this->url = \lib\router::get_url();
		$this->api_key();
		$this->permission();
		if(!debug::$status)
		{
			$this->_processor(['force_stop' => true]);
		}
		return parent::_call($_name, $_args, $parm);
	}


	/**
	 * check api key and set the user id
	 */
	public function api_key()
	{

		$api_token = utility::header("api_token") ? utility::header("api_token") : utility::header("Api_token");

		$authorization = utility::header("authorization");
		if($api_token)
		{
			$authorization = $api_token;
		}
		elseif(!$authorization)
		{
			$authorization = utility::header("Authorization");
		}

		if(!$authorization)
		{
			return debug::error('Authorization not found', 'authorization', 'access');
		}

		if($authorization === '**Ermile**7o6mP43MwBvHT7k2QBjut5nUnEoYtHf0DUbk807ThXIZjR^^Telegram^^')
		{
			$this->telegram_token();
		}
		else
		{
			$token = token::get_type($authorization);

			if(!debug::$status)
			{
				return false;
			}

			switch ($token)
			{

				case 'user_token':
				case 'guest':
					if($this->url == 'v1/token/login' || $this->url == 'v1/token/guest')
					{
						debug::error(T_("Access denide (Invalid url)"), 'authorization', 'access');
						return false;
					}

					if(!token::check($authorization, $token))
					{
						return false;
					}

					$user_id = token::get_user_id($authorization);

					if(!$user_id)
					{
						debug::error(T_("Invalid authorization key (User not found)"), 'authorization', 'access');
						return false;
					}

					$this->user_id = $user_id;
					break;

				case 'api_key':
					if($this->url != 'v1/token/temp' && $this->url != 'v1/token/guest' && $this->url != 'v1/token/login')
					{
						debug::error(T_("Access denide to load this url by api key"), 'authorization', 'access');
						return false;
					}
					break;

				default :
					debug::error(T_("Invalid token"), 'authorization', 'access');
					return false;
			}

			if(isset(token::$PARENT['value']))
			{
				$this->parent_api_key = token::$PARENT['value'];
			}

			if(isset(token::$PARENT['user_id']))
			{
				$this->parent_api_key_user_id = token::$PARENT['user_id'];
			}
		}

		$this->authorization = $authorization;
	}


	public function telegram_token()
	{
		$telegram_id = utility::header("tg_id");
		$first_name  = utility::header('tg_first_name');
		$last_name   = utility::header('tg_last_name');
		$username    = utility::header('tg_username');
		$started     = utility::header('tg_start');
		$ref         = utility::header('tg_ref');

		if(!$telegram_id)
		{
			debug::error(T_("Telegram id not found"), 'telegram_id', 'header');
			return false;
		}

		if(!is_numeric($telegram_id))
		{
			debug::error(T_("Invalid telegram id"), 'telegram_id', 'header');
			return false;
		}

		$check_exist_user =
		[
			'option_cat'   => 'telegram',
			'option_key'   => 'id',
			'option_value' => $telegram_id,
			'limit'        => 1
		];
		$check_exist_user = \lib\db\options::get($check_exist_user);
		if(empty($check_exist_user))
		{
			// if user does not exist in db, signup it

			// calc full_name of user
			$fullName = trim($first_name. ' '. $last_name);
			$fullName = \lib\utility\safe::safe($fullName, 'sqlinjection');
			$mobile = 'tg_'. $telegram_id;

			if(mb_strlen($mobile) > 15)
			{
				debug::error(T_("Invalid telegram id leng"), 'telegram_id', 'header');
				return false;
			}

			$user = \lib\db\users::get_by_mobile($mobile);
			if(empty($user))
			{
				$port = $started ? 'telegram' : 'telegram_guest';

				$this->user_id = \lib\db\users::signup(
				[
					'mobile'      => $mobile,
					'password'    => null,
					'permission'  => true,
					'user_verify' => 'uniqueid',
					'displayname' => $fullName,
					'ref'         => $ref,
					'port'        => $port, // telegram|telagram_guest; the users answer the inline keyboard or in bot
					'subport'     => null, // the group code or chanal code
				]);

				// save telegram user detail like name and username into options
				$insert_options =
				[
					'user_id'      => $this->user_id,
					'option_cat'   => 'telegram',
					'option_key'   => 'id',
					'option_value' => $telegram_id,
					'option_meta'  => json_encode(
					[
						'id'         => $telegram_id,
						'first_name' => $first_name,
						'last_name'  => $last_name,
						'username'   => $username
					], JSON_UNESCAPED_UNICODE),
				];
				// save in options table
				\lib\db\options::insert($insert_options);
			}
			elseif(isset($user['id']) && is_numeric($user['id']))
			{
				$this->user_id = (int) $user['id'];
			}
		}
		elseif(isset($check_exist_user['user_id']) && is_numeric($check_exist_user['user_id']))
		{
			$this->user_id = (int) $check_exist_user['user_id'];
		}
		else
		{
			debug::error(T_("System error"));
			return false;
		}
	}


	/**
	 * save api log
	 *
	 * @param      boolean  $options  The options
	 */
	public function _processor($options = false)
	{
		$log = [];

		if(isset($_SERVER['REQUEST_URI']))
		{
			$log['url'] = $_SERVER['REQUEST_URI'];
		}

		if(isset($_SERVER['REQUEST_METHOD']))
		{
			$log['method'] = $_SERVER['REQUEST_METHOD'];
		}

		if(isset($_SERVER['REDIRECT_STATUS']))
		{
			$log['pagestatus'] = $_SERVER['REDIRECT_STATUS'];
		}

		$log['request']        = json_encode(\lib\utility::request(), JSON_UNESCAPED_UNICODE);
		$log['debug']          = json_encode(\lib\debug::compile(), JSON_UNESCAPED_UNICODE);
		$log['response']       = json_encode(\lib\debug::get_result(), JSON_UNESCAPED_UNICODE);
		$log['requestheader']  = json_encode(\lib\utility::header(), JSON_UNESCAPED_UNICODE);
		$log['responseheader'] = json_encode(apache_response_headers(), JSON_UNESCAPED_UNICODE);
		$log['status']         = \lib\debug::$status;
		$log['token']          = $this->authorization;
		$log['user_id']        = $this->user_id;
		$log['apikeyuserid']   = $this->parent_api_key_user_id;
		$log['apikey']         = $this->parent_api_key;
		$log['clientip']       = ClientIP;
		$log['visit_id']       = null;

		$log                   = \lib\utility\safe::safe($log);

		\lib\db\apilogs::insert($log);

		parent::_processor($options);
	}
}
?>