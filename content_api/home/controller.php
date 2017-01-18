<?php
namespace content_api\home;
use \lib\utility;
use \lib\debug;

class controller extends  \mvc\controller
{
	use \content_api\answer\controller;
	use \content_api\like\controller;
	use \content_api\fav\controller;
	use \content_api\feedback\controller;
	use \content_api\file\controller;
	use \content_api\logintoken\controller;
	use \content_api\poll\controller;
	use \content_api\search\controller;
	use \content_api\tag\controller;


	/**
	 * the user id
	 *
	 * @var        integer
	 */
	public $user_id = 0;


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
		$url = \lib\router::get_url(0);
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
	 * { function_description }
	 */
	public function api_key()
	{

		if(utility::header('authorization') || utility::header('Authorization'))
		{
			$api_key = utility::header('authorization') ? utility::header('authorization') : utility::header('Authorization');
			$arg_check =
			[
				'option_cat'   => 'token',
				'option_value' => $api_key,
				'limit'        => 1
			];
			$check = \lib\db\options::get($arg_check);

			if(empty($check) || !$check)
			{
				\lib\debug::error('Authorization failed', 'authorization', 'access');
			}

			if(isset($check['key']))
			{
				\lib\db\options::insert([
				'option_cat' => 'token',
				'option_value' => $api_key,
				'limit' => 1
				]);
			}


		}
		else
		{
			\lib\debug::error('Authorization not found', 'authorization', 'access');
		}

		// var_dump(utility::request());
		// exit();
	}
}
?>