<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
class callback_query
{
	public static $message_result = [];
	public static $answer_message_result = [];
	public static function start($_query = null)
	{
		$result = ['method' => 'answerCallbackQuery'];
		$result['callback_query_id'] = $_query['id'];
		if(array_key_exists('game_short_name', $_query))
		{
			$result['url'] = 'https://sarshomar.com/game';
			return $result;
		}
		$data_url = preg_split("[\/]", $_query['data']);
		if(count($data_url) < 1)
		{
			session::remove_back('expire', 'inline_cache');
			return $result;
		}

		/**
		 * check if unique request
		 */
		$force_inline = false;
		$sub_port = false;
		if(array_key_exists('inline_message_id', $_query))
		{
			self::$message_result['inline_message_id'] = $sub_port = $_query['inline_message_id'];
			if(\lib\storage::get_is_new_user())
			{
				$get = \lib\db\options::get([
					'option_cat'	=> 'telegram',
					'option_key'	=> 'subport',
					'option_meta'	=> self::$message_result['inline_message_id'],
					'limit'			=>1
					]);
				$poll = \lib\db\polls::get_poll($get['post_id']);
				if($poll > 1000)
				{
					\ilib\db\users::update(['user_parent' => $get['user_id']], bot::$user_id);
				}
				callback_query\language::set($poll['language']);
				if($poll['language'] == 'fa')
				{
					\lib\db\units::set_user_unit(bot::$user_id,'toman');
				}
			}
			$force_inline = true;
		}
		elseif(array_key_exists('chat_instance', $_query))
		{
			self::$message_result['chat_instance'] = $_query['chat_instance'];
			preg_match("/^(\d+_\d+):(.*)$/", $data_url[0], $unique_id);

			$data_url[0] = $unique_id[2];
			$unique_id = !is_null($unique_id[1]) ? 'ik_' . $unique_id[1] : null;
			$callback_session = session::get('tmp', 'callback_query', $unique_id);
			if((is_null($unique_id) || is_null($callback_session)) && !$force_inline)
			{
				// session::remove_back('expire', 'inline_cache');
				// return $result;
			}
			$callback_query = (array) session::get('tmp', 'callback_query');
			if(isset($callback_query[$unique_id]))
			{
				unset($callback_query[$unique_id]);
			}
			session::set('tmp', 'callback_query', $callback_query);
		}


		/**
		 * check type
		 */
		$callback_result = [];
		$class_name = '\content\saloos_tg\sarshomar_bot\commands\callback_query\\' . $data_url[0];
		self::$answer_message_result = $result;
		if(class_exists($class_name) && method_exists($class_name, 'start'))
		{
			$callback_result = $class_name::start($_query, $data_url);
			$callback_result = is_array($callback_result) ? $callback_result : [];
		}

		return array_merge($result, $callback_result);
	}

	public static function edit_message($_result, $_return = false)
	{
		$response = [
			"method" 				=> "editMessageText",
			'parse_mode' => 'HTML',
			'disable_web_page_preview' => true,
			];
		$response = array_merge($response, self::$message_result);
		$response = array_merge($response, $_result);
		$response['parse_mode'] = 'HTML';
		if(isset($response['caption']))
		{
			$response['method'] = 'editMessageCaption';
		}
		if($_return)
		{
			return $response;
		}
		if(!isset($response['text']) && !isset($response['caption']))
		{
			return;
		}
		$return = bot::sendResponse($response);
		return $return;
	}

	public static function answer_message($_result = [])
	{
		if(!is_array($_result))
		{
			$_result = [];
		}
		return bot::sendResponse(array_merge(self::$answer_message_result, $_result));
	}
}
?>