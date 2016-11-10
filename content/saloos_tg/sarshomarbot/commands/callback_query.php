<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
class callback_query
{
	public static $message_result = [];
	public static function start($_query = null)
	{
		$data_url = preg_split("[\/]", $_query['data']);
		$result = ['method' => 'answerCallbackQuery'];
		$result['callback_query_id'] = $_query['id'];
		if(count($data_url) < 1)
		{
			session::remove_back('expire', 'inline_cache');
			return $result;
		}
		preg_match("/^(\d+\.\d+):(.*)$/", $data_url[0], $unique_id);
		if(count($unique_id) < 2)
		{
			session::remove_back('expire', 'inline_cache');
			return $result;
		}
		$data_url[0] = $unique_id[2];
		$unique_id = $unique_id[1];
		/**
		 * check if unique request
		 */
		$callback_session = session::get('tmp', 'callback_query');
		if(!$callback_session)
		{
			$callback_session = [];
		}
		elseif(!is_array($callback_session))
		{
			$callback_session = [$callback_session];
		}

		if(array_search($unique_id, $callback_session) === false)
		{
			array_push($callback_session, $unique_id);
			session::set('tmp', 'callback_query', $callback_session);
		}
		else
		{
			session::remove_back('expire', 'inline_cache');
			return $result;
		}

		/**
		 * check type
		 */
		if(array_key_exists('inline_message_id', $_query))
		{
			self::$message_result['inline_message_id'] = $_query['inline_message_id'];
		}
		elseif(array_key_exists('chat_instance', $_query))
		{
			self::$message_result['chat_instance'] = $_query['chat_instance'];
		}
		$callback_result = [];
		$class_name = '\content\saloos_tg\sarshomarbot\commands\callback_query\\' . $data_url[0];
		if(class_exists($class_name) && method_exists($class_name, 'start'))
		{
			$callback_result = $class_name::start($_query, $data_url);
			$callback_result = is_array($callback_result) ? $callback_result : [];
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
		$answer = \lib\utility\answers::save(bot::$user_id, $poll_id, $data[4]);
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

	public static function edit_message($_result, $_return = false)
	{
		$response = [
			"method" 				=> "editMessageText",
			'parse_mode' => 'Markdown',
			'disable_web_page_preview' => true,
			];
		$response = array_merge($response, self::$message_result);
		$response = array_merge($response, $_result);
		if($_return)
		{
			return $response;
		}
		bot::sendResponse($response);
	}
}
?>