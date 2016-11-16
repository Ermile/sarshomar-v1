<?php
namespace content\poll;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("poll","poll")->ALL("/^\\$\/(([23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+)(\/(.+))?)$/");
		$this->post("save_answer")->ALL("/^\\$\/[23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+\/(.*)$/");
	}
}
?>