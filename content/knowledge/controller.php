<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get(false,"all")->ALL("$");
	}
}
?>