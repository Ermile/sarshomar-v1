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
	public $authorization = null;


	use tools\get_token;
	use tools\ready;


	/**
	 * set permission of this user
	 */
	public function permission()
	{
		if($this->user_id)
		{
			$user_perm = \lib\db\users::get_user_data($this->user_id, 'user_permission');
			if(isset($user_perm['user_permission']) && is_numeric($user_perm['user_permission']))
			{
				$args                         = [];
				$args['user']['permission']   = $user_perm['user_permission'];
				$args['permission']           = $this->setPermissionSession($user_perm['user_permission'], true);
				permission::$get_from_session = false;
				permission::$PERMISSION       = $args;
			}
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
		$authorization = utility::header("authorization");

		if(!$authorization)
		{
			$authorization = utility::header("Authorization");
		}

		if(!$authorization)
		{
			return debug::error('Authorization not found', 'authorization', 'access');
		}

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

				$user_id = token::get_user_id($authorization);

				if(!$user_id)
				{
					debug::error(T_("Invalid authorization kye (user not found)"), 'authorization', 'access');
					return false;
				}

				$this->user_id = $user_id;
				break;

			case 'api_key':
				if($this->url != 'v1/token/temp' && $this->url != 'v1/token/guest' && $this->url != 'v1/token/login')
				{
					debug::error(T_("Access denide (Invalid url)"), 'authorization', 'access');
					return false;
				}
				break;

			default :
				debug::error(T_("Invalid token"), 'authorization', 'access');
				return false;
		}

		$this->authorization = $authorization;
	}
}
?>