<?php
namespace content\saloos_tg\sarshomar_bot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;
use \lib\main;
use \lib\debug;

class step_tour
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('tour');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		if($_text)
		{
			step::stop();
			$count = \saloos::lib_static('db')->users()::get_count('all');
			$result = [];
			$result["reply_markup"] = menu::main(true);
			$result["text"] = T_("Welcome to the society of :count people of sarshomar", ['count' => utility::nubmer_language($count)]);
			bot::sendResponse($result);
		}
		else
		{
			bot::sendResponse([
				'response_callback' => utility::response_expire('tour'),
				'method' => 'sendvideo',
				'video' => 'BAADBAADM3IAAh0bZAcE5qtgyQnHVAI',
				'caption' => 'ویدئوی بالا به شما کمک می‌کند تا بهتر با ربات سرشمار کار کنید.',
				'reply_markup' => [
					'inline_keyboard' => [[['text' => 'خب', 'callback_data'=>'tour/end']]]
				]
				]);
		}
	}
}
?>