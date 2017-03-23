<?php
namespace content\search;
use \lib\saloos;

class controller extends \content\main\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		parent::_route();
		$this->get("search")->ALL("/.*/");
	}
}
?>