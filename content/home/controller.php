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
		if(\lib\router::get_url(0) == '$')
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return ;
		}

		$this->post("random_result")->ALL("");

		// $this->get("tags","tags")->ALL("/(.*)/");
	}
}
?>