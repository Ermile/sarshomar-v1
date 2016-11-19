<?php
namespace content\home;
use \lib\saloos;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		if(\lib\router::get_url() == 'contact')
		{
			\lib\router::set_controller("\\content\\contact\\controller");
			return;
		}
		$reg = "/^\\$\/(([23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+)(\/(.+))?)$/";
		if(preg_match($reg, \lib\router::get_url(), $controller_name))
		{
			if(isset($controller_name[4]) && $controller_name[4] == 'comments')
			{
				\lib\router::set_controller("\\content\\comments\\controller");
			}
			else
			{
				\lib\router::set_controller("\\content\\poll\\controller");
			}
			return;
		}

		if(substr(\lib\router::get_url(), 0, 1) == '$')
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return;
		}

		$this->get('tg_session', false)->ALL("#^tg_session/(json|object)/(\d+)$#");

		$this->post("random_result")->ALL("");

		$this->get("ask")->ALL("ask");

		$this->get("random")->ALL("ask/random");
	}
}
?>