<?php
namespace content_api\home;
use \lib\utility;
use \lib\debug;
use \lib\utility\token;

class controller extends  \mvc\controller
{
	use \content_api\answer\controller;
	use \content_api\budget\controller;
	use \content_api\fav\controller;
	use \content_api\feedback\controller;
	use \content_api\file\controller;
	use \content_api\guesttoken\controller;
	use \content_api\like\controller;
	use \content_api\logintoken\controller;
	use \content_api\poll\controller;
	use \content_api\calcprice\controller;
	use \content_api\search\controller;
	use \content_api\tag\controller;


	/**
	 * the user id
	 *
	 * @var        integer
	 */
	public $user_id = 0;


	/**
	 * the url
	 *
	 * @var        <type>
	 */
	public $url = null;


	/**
	 * the short url
	 *
	 * @var        string
	 */
	public static $shortURL = \lib\utility\shortURL::ALPHABET;

	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();

		$this->url = \lib\router::get_url(0);

		$this->api_key();
	}

	/**
	 * route url like this:
	 * post > poll/ to add poll
	 * get poll/[shorturl] to get poll
	 * put poll/[shorturl] to edit poll
	 * delete poll/[shorturl] to delete poll
	 */
	public function _route()
	{
		$url = $this->url;
		if(preg_match('/^(add|get|edit|delete)?(.*)$/', $url, $_class))
		{
			$class = strtolower($_class[2]);
			$route_method = strtolower('route_'.$class);
			if(method_exists($this, $route_method))
			{
				$this->model_name = '\content_api\\' . $class . '\model';
				// set user id
				$this->model()->user_id = $this->user_id ;

				call_user_func([$this, $route_method]);
			}
		}
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
				if($this->url == 'loginToken' || $this->url == 'guestToken')
				{
					debug::error(T_("Access denide (Invalid url)"), 'authorization', 'access');
				}

				$user_id = token::get_user_id($authorization);

				if(!$user_id)
				{
					debug::error(T_("Invalid authorization kye (user not found)"), 'authorization', 'access');
				}

				$this->user_id = $user_id;
				break;

			case 'api_key':
				if($this->url != 'loginToken' && $this->url != 'guestToken')
				{
					debug::error(T_("Access denide (Invalid url)"), 'authorization', 'access');
				}
				break;

			default :
				debug::error(T_("Invalid token"), 'authorization', 'access');

		}

	}
}
?>