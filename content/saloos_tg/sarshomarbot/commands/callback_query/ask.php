<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\step_sarshomar;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class ask
{
	public static function start($_query, $_data_url)
	{
		$type = $_data_url[1];
		bot::sendResponse(step_sarshomar::step1());
			handle::send_log(["expire" => array_key_exists('ask_result_response', $_SESSION['tg'])]);
			handle::send_log(["if_expire" => $_SESSION['tg']]);
		if(array_key_exists('ask_result_response', $_SESSION['tg']))
		{
			handle::send_log(["ask" => true]);
			$response = $_SESSION['tg']['ask_result_response'];
			$edit_return = [
				"method" 					=> "editMessageReplyMarkup",
				"chat_id" 					=> $response['result']['chat']['id'],
				"message_id" 				=> $response['result']['message_id'],
				"reply_markup"				=> []
				];
			callback_query::edit_message($edit_return);
		}
		return ["text" => "hi hey"];
	}
}
?>