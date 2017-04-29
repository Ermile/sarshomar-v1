<?php
namespace content\contact;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		$this->get(false, false)->ALL("/contact/");
		$this->post("contact")->ALL("/contact/");
		// $this->get()
	}
}
?>