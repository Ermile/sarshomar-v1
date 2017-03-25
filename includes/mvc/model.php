<?php
namespace mvc;

class model extends \lib\mvc\model
{
	use \content\enter\tools\login;

	public function mvc_login_by_remember()
	{
		$url = \lib\utility\safe::safe($_SERVER['REQUEST_URI']);
		$this->login_by_remember($url);
	}
}
?>