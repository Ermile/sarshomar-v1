<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait login
{
	/**
	 * login
	 */
	public function login_set()
	{
		$myfields =
		[
			'id',
			'user_displayname',
			'user_mobile',
			'user_meta',
			'user_status',
		];

		$this->setLoginSession($this->user_data, $myfields);
		$this->redirector('@')->redirect();
		debug::msg('direct', true);
	}
}
?>