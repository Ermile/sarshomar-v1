<?php
namespace content\comments;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get(false, false)->ALL("/^\\$\/(([23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+)\/comments)$/");
	}
}
?>