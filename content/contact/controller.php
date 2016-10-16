<?php
namespace content\contact;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->post("contact")->ALL();
	}
}
?>