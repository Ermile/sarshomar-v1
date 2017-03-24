<?php
namespace mvc;

class model extends \lib\mvc\model
{
	use \content\enter\tools\login;

	public function mvc_login_by_remember()
	{
		$this->login_by_remember();
	}
}
?>