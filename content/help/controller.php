<?php
namespace content\help;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		if($this->url('child') == null)
		{
			$this->get(false, false)->ALL("help");
			$this->post("search")->ALL("help");
			// $this->route_check_true = true;
		}
		else
		{
			\lib\router::set_controller('\content\home\controller');
		}
	}
}
?>