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
		if(preg_match("/^\\$\/(([23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+)(\/(.+))?)$/", \lib\router::get_url()))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}

		if(substr(\lib\router::get_url(), 0, 1) == '$')
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return;
		}

		$this->post("random_result")->ALL("");

		// $this->get("tags","tags")->ALL("/(.*)/");
	}
}
?>