<?php
namespace content_api\home;

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
	 * the short url
	 *
	 * @var        string
	 */
	public static $shortURL = \lib\utility\shortURL::ALPHABET;

	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
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
				call_user_func([$this, $route_method]);
			}
		}
	}
}
?>