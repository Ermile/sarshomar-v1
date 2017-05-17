<?php
namespace content\saloos_tg\germile_bot;
// use telegram class as bot
use \lib\telegram\tg as bot;

class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/germile_bot/'.\lib\option::social('telegram', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key     = '216549449:AAG4IXa9gFLJSrSrjdTbhWhbAzhpCkRngvo';
			bot::$name        = 'germile_bot';
			bot::$cmdFolder   = '\\'. __NAMESPACE__ .'\commands\\';
			bot::$defaultText = 'تعریف نشده';
			bot::$defaultMenu = commands\menu::main(true);
			bot::$fill        =
			[
				'name'     => 'گرمایل',
				'fullName' => 'فست فود بزرگ گرمایل',
				// 'about'    => $txt_about,
			];
			$result         = bot::run();

			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>