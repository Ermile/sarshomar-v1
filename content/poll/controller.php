<?php
namespace content\poll;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("poll","poll")->ALL("/^sp\_([". self::$shortURL. "]+)$/");
		$this->get("poll","poll")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->get("poll","poll")->ALL("/^\\$([". self::$shortURL. "]+)$/");
		$this->get("realpath","poll")->ALL("/.*/");
		// $this->post("save_answer")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->post("save_answer")->ALL("/.*/");
	}
}
?>