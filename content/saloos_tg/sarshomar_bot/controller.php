<?php
namespace content\saloos_tg\sarshomar_bot;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/sarshomar_bot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key   = '186535040:AAGKVOlmlpA4wU0Vjv0-s93w_o2aB3n0xKE';
			bot::$name      = 'sarshomar_bot';
			// bot::$cmdFolder = '\\'. __NAMESPACE__ .'\commands\\';
			bot::$defaultText = 'تعریف نشده';

			$result         = bot::run(true);

			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>