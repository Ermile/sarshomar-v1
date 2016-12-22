<?php
namespace content\help;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		if($this->url('child') == null)
		{
			$this->get(false, false)->ALL("help");
			// $this->route_check_true = true;
		}
		else
		{
			\lib\router::set_controller('\content\home\controller');
		}
	}
}
?>