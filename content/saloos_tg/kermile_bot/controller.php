<?php
namespace content\saloos_tg\kermile_bot;
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
		$myhook = 'saloos_tg/kermile_bot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key   = '209285392:AAE3FdlRpJ2hB6k9tfs5j9RlwTQqnJar8ws';
			bot::$name      = 'kermile_bot';
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