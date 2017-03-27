<?php
namespace content\referer;

class controller extends \mvc\controller
{
	use token\controller;
	use instagram\controller;
	function _route()
	{

		$referer = \lib\utility::get('to');
		$class_name = null;
		if(preg_match("/([^:]+):(.*)/", $referer, $_referer) && $referer)
		{
			$class_name = mb_strtolower($_referer[1]);
			if($class_name == 'http' || $class_name == 'https')
			{
				\lib\debug::msg('direct', true);
				$this->redirector($referer)->redirect();
			}
		}
		elseif($referer)
		{
			\lib\debug::msg('direct', true);
			$this->redirector($referer)->redirect();
		}
		elseif(\lib\router::get_url(1))
		{
			$class_name = \lib\router::get_url(1);
		}
		if($class_name)
		{
			$model_name = '\content\referer\\'. $class_name . '\model';
			$view_name = '\content\referer\\'. $class_name . '\view';

			if(method_exists($this, 'route_'.$class_name))
			{
				$route = call_user_func([$this, 'route_'.$class_name]);
			}
			else
			{
				$route = true;
			}

			if($route && class_exists($model_name))
			{
				$this->model_name = $model_name;
			}
			if($route && class_exists($view_name))
			{
				$this->view_name = $view_name;
			}
		}

		$this->get('ref', 'ref')->ALL('ref');
	}

	function check_for_login($_referer = null)
	{
		if(!$this->login())
		{
			$referer = $_referer;
			if(!$_referer)
			{
				$referer = \lib\utility::get('to');
			}
			$referer_link = $referer ? '?referer='.$referer : '';
			\lib\debug::msg('direct', true);
			$this->redirector()->set_url('account/login'.$referer_link)->redirect();
		}
		return true;
	}
}

?>