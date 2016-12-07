<?php
namespace content\search;
use \lib\saloos;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->get("search")->ALL("/.*/");
	}
}
?>