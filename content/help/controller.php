<?php
namespace content\help;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get(false, false)->ALL("help");
	}
}
?>