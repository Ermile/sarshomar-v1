<?php
namespace content\help;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$url = \lib\router::get_url();
		$this->get(false, false)->ALL("/help/");
	}
}
?>