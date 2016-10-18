<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class callback_query
{
	use callback_query\set_language;
	public static $message_result = [];
	public static function start($_query = null)
	{

		$data_url = preg_split("[\/]", $_query['data']);

		$result = ['method' => 'answerCallbackQuery'];
		$result['callback_query_id'] = $_query['id'];

		if(array_key_exists('inline_message_id', $_query))
		{
			self::$message_result['inline_message_id'] = $_query['inline_message_id'];
		}
		else{
			self::$message_result['chat_instance'] = $_query['chat_instance'];
		}
		$callback_result = [];
		if(method_exists('\content\saloos_tg\sarshomarbot\commands\callback_query', $data_url[0]))
		{
			$callback_result = self::{$data_url[0]}($_query, $data_url);
		}
		return array_merge($result, $callback_result);


		preg_match("/^((last|cancel)\/)?\\$\/([^\/]+)\/(\d+)$/", $_query['data'], $data);

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

	public static function edit_message($_result)
	{
		$response = [
			"method" 				=> "editMessageText",
			'parse_mode' => 'Markdown',
			'disable_web_page_preview' => true,
			];
		$response = array_merge($response, self::$message_result);
		bot::sendResponse(array_merge($response, $_result));
	}
}
?>