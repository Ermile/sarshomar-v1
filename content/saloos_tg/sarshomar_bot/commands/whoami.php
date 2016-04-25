<?php
namespace content\saloos_tg\sarshomar_bot\commands;

trait whoami
{
	public static $name = 'سلام';
	static function exec()
	{
		var_dump('exec');
	}
}
?>