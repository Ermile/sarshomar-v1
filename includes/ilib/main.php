<?php
namespace ilib;
use \lib\router;

class main extends \lib\main
{

	/**
	 * (content_u) u change to @
	 */
	function __construct()
	{
		// redirect /u url to /@
		if(router::get_repository_name() == 'content_u')
		{
			$redirect = new \lib\redirector();
			$redirect->url = \lib\define::get_current_language_string() . '/@/' . $redirect->get_url();
			$redirect->redirect();
		}

		if(router::get_class() == '@')
		{
			\lib\storage::set('rep', 'u');
			$request_url = $_SERVER['REQUEST_URI'];
			if(\lib\utility\location\languages::check(substr($request_url, 1, 2)))
			{
				$request_url = substr($request_url, 3);
			}
			$url = preg_replace("/^\/@/", '', $request_url);
			new router("$url");
			self::$myrep        = router::set_repository('content_u', true);
		}
		self::$url_property = router::get_url_property(-1);
		self::$myrep        = router::get_repository_name();
		$this->controller_finder();
	}


	/**
	 * Adds controller tracks.
	 * check url[2] and load controller
	 */
	function add_controller_tracks()
	{
		if(router::get_repository_name() == 'content_api' && router::get_url(2))
		{
			$this->add_track('api_childs', function()
			{
				$controller_name  = '\\'. self::$myrep;
				$controller_name .= '\\'. router::get_class();
				$controller_name .= '\\'. router::get_method();
				$controller_name .= '\\'. router::get_url(2);
				$controller_name .= '\\controller';
				return $this->check_controller($controller_name);
			});
		}
		parent::add_controller_tracks();
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_controller_name  The controller name
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	function check_controller($_controller_name)
	{
		$default_controller = parent::check_controller($_controller_name);
		if(!$default_controller)
		{
			$controller_name = '\addons'. $_controller_name;
			if(!class_exists($controller_name))
			{
				return NULL;
			}
			else
			{
				return $controller_name;
			}
		}
		return $default_controller;
	}
}
?>