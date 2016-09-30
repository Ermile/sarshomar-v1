<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("all","all")->ALL();
		$this->get("poll","poll")->ALL("/^knowledge\/(sp\_)([23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+)\/(.*)$/");
	}
}
?>