<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class simple extends \content\saloos_tg\sarshomar_bot\controller
{
	public static function userid()
	{
		self::$text = 'Your userid: '. bot::response('from');
	}

	public static function test()
	{
		self::$text = 'Test *Saloos* bot on '. Domain;
	}

	public static function say($_text)
	{
		self::$text = $_text;
	}
}
?>