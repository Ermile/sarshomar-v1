<?php
namespace content\comments;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		$this->get(false, false)->ALL("/^\\$\/(([". self::$shortURL. "]+)\/comments)$/");
	}
}
?>