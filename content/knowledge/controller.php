<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("all","all")->ALL("$");
		$this->get("poll","poll")->ALL("/^\\$\/[23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+\/(.*)$/");

		$this->post("save_answer")->ALL("/^\\$\/[23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+\/(.*)$/");
	}
}
?>