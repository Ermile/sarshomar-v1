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
		register_shutdown_function(function()
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", json_encode(error_get_last()));
		});
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

			/**
			 * start hooks and run telegram session from db
			 */
			bot::hook();
			\lib\db\tg_session::$user_id = bot::$user_id;
			\lib\db\tg_session::start(bot::$user_id);

			/**
			 * run telegram handle
			 */
			$result           = bot::run(true);

			/**
			 * save telegram sessions to db
			 */
			\lib\db\tg_session::save(bot::$user_id);


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