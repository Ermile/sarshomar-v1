<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class callback_query
{

	public static function start($_query = null)
	{
		$callback = $_query;
		$inline_message_id = $_query['inline_message_id'];
		preg_match("/^((last|cancel)\/)?\\$\/([^\/]+)\/(\d+)$/", $_query['data'], $data);
		$result = ['method' => 'answerCallbackQuery'];
		$result['callback_query_id'] = $callback['id'];
		if(empty($data))
		{
			// is fatal error and hack error
			$result['text'] = "❌ پاسخ درست نمی‌باشد ";
			return $result;
		}elseif($data[2] == 'cancel')
		{
			$result['text'] = "⭕️ شاید کمتر از این موارد مشاهده کنید";
			return $result;
		}else
		{
			$result['text'] = '👍 پاسخ ' . ($data[4] + 1) . " برای شما ثبت شد";
		}

		$poll_id = \lib\utility\shortURL::decode($data[3]);
		$answer = \lib\db\answers::save(bot::$user_id, $poll_id, $data[4]);
		$poll = \lib\db\polls::get_poll($poll_id);
		$poll_result = poll_result::make($poll);
		$message = $poll_result['message'];
		poll_result::add_message($message, rand(123568, 999999) . " رای دادند", 3);
		$inline_keyboard = $poll_result['inline_keyboard'];

		bot::sendResponse([
			"method" 				=> "editMessageText",
			"inline_message_id" 	=> $inline_message_id,
			"text"					=> poll_result::get_message($message),
			'parse_mode' => 'Markdown',
			'disable_web_page_preview' => true,
			"reply_markup" 			=> ["inline_keyboard" => $inline_keyboard]
			]);
		return $result;
	}
}
?>