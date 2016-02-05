<?php
namespace content\telegram;
use \lib\debug;

class model extends \lib\model
{
	public function post_tg($object)
	{
		require_once (addons. '/lib/TelegramBot/src/BotApi.php');
		require_once (addons. '/lib/TelegramBot/src/BaseType.php');
		require_once (addons. '/lib/TelegramBot/src/TypeInterface.php');
		require_once (addons. '/lib/TelegramBot/src/Types/Message.php');
		require_once (addons. '/lib/TelegramBot/src/Types/User.php');
		require_once (addons. '/lib/TelegramBot/src/Types/Chat.php');
		require_once (addons. '/lib/TelegramBot/src/Types/ReplyKeyboardMarkup.php');
		require_once (addons. '/lib/TelegramBot/src/Types/ReplyKeyboardMarkup.php');
		$bot = new \TelegramBot\Api\BotApi('142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44');
		
		$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array("one - ".rand(1,9), "two - ".rand(1,9), "three - ".rand(1,9))), true);
		
		$telegram_hooks_get = file_get_contents('php://input');

		$telegram_hooks_get = json_decode($telegram_hooks_get);
		

		$text = $telegram_hooks_get->message->text;
		$chat_id = $telegram_hooks_get->message->chat->id;
		$x = $bot->sendMessage($chat_id, "question : $text", 'Markdown', false, null, $keyboard);

		return json_decode(json_encode($x));

	}
}
?>