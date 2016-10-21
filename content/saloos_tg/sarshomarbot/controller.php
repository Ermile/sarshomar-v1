<?php
namespace content\saloos_tg\sarshomarbot;
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
		$myhook = 'saloos_tg/sarshomarbot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key     = '142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44';
			bot::$name        = 'sarshomarbot';
			bot::$cmdFolder   = '\\'. __NAMESPACE__ .'\commands\\';
			bot::$defaultText = T_('Undefined');
			bot::$defaultMenu = function(){
				return commands\menu::main(true);
			};
			bot::$once_log	  = false;
			bot::$fill        =
			[
				'name'     => T_('Sarshomar'),
				'fullName' => T_('Sarshomar'),
				// 'about'    => $txt_about,
			];
			$result           = bot::run(true);
			if (bot::$defaultText == T_('Undefined'))
			{
				bot::sendResponse(["method" => "sendMessage", "chat_id" => 58164083, "text" => "🚷 auto ftp is off"]);
			}
			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>