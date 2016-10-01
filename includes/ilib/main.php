<?php
namespace ilib;
class main extends \lib\main{
	function __construct()
	{
		if(\lib\router::get_class() == '@')
		{
			$url = preg_replace("/^\/@/", '', $_SERVER['REQUEST_URI']);
			new \lib\router("$url");
			self::$myrep        = \lib\router::set_repository('content_u', true);
		}
		self::$url_property = \lib\router::get_url_property(-1);
		self::$myrep        = \lib\router::get_repository_name();
		$this->controller_finder();
	}
	function check_controller($_controller_name){
		$default_controller = parent::check_controller($_controller_name);
		if(!$default_controller){
			$controller_name = '\addons'. $_controller_name;
			if(!class_exists($controller_name))
				return NULL;
			else
				return $controller_name;
		}
		return $default_controller;
	}
}
?>