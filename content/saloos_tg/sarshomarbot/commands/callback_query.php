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
		preg_match("/^(\d+_\d+):(.*)$/", $data_url[0], $unique_id);
		$data_url[0] = $unique_id[2];
		$unique_id = !is_null($unique_id[1]) ? 'ik_' . $unique_id[1] : null;
		$callback_session = session::get('tmp', 'callback_query', $unique_id);

		/**
		 * check if unique request
		 */
		$force_inline = false;
		if(array_key_exists('inline_message_id', $_query))
		{
			self::$message_result['inline_message_id'] = $_query['inline_message_id'];
			$force_inline = true;
		}
		elseif(array_key_exists('chat_instance', $_query))
		{
			self::$message_result['chat_instance'] = $_query['chat_instance'];
		}

		if((is_null($unique_id) || is_null($callback_session)) && !$force_inline)
		{
			session::remove_back('expire', 'inline_cache');
			return $result;
		}

		/**
		 * check type
		 */
		$callback_result = [];
		$class_name = '\content\saloos_tg\sarshomarbot\commands\callback_query\\' . $data_url[0];
		if(class_exists($class_name) && method_exists($class_name, 'start'))
		{
			$callback_result = $class_name::start($_query, $data_url);
			$callback_result = is_array($callback_result) ? $callback_result : [];
		}

		$callback_query = (array) session::get('tmp', 'callback_query');
		unset($callback_query[$unique_id]);
		session::set('tmp', 'callback_query', $callback_query);

		return array_merge($result, $callback_result);
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
		$return = bot::sendResponse($response);
		return $return;
	}
}
?>