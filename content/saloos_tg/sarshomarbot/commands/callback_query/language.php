<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;
use \lib\telegram\step;

class language
{
	static $user_language = null;
	static $valid_lang = [
	'fa_IR' => ['فارسی', 'fa', 'persian', 'farsi', 'fa_ir', 'fa-ir'],
	'en_US' => ['en', 'english']
	];
	public static function start($_query, $_data_url)
	{
		$get = self::check();
		if(empty($get))
		{
			self::set($_data_url[1], ["ref" => "callback_query"]);
			$lang_name = $_data_url[1];
			$lang = preg_replace("[_]", "\\\\_", $lang_name);
		}
		else
		{
			$lang_name = $get;
			$lang = preg_replace("[_]", "\\\\_", $get);
		}
		callback_query::edit_message([
			'text' => 'Language set '. $lang
			]);
		bot::sendResponse([
			"method" => "sendMessage",
			"text" => T_("Welcome"),
			"reply_markup" => menu::main(true)
			]);
		return ['text' => '🗣 Your language set : ' . $lang_name];
	}

	public static function make_result($_value)
	{
		$return = false;
		$edit_return = false;
		$get_back_response = session::get('tmp', 'language_inline');
		$get_sesstion = "SELECT * FROM options
		WHERE options.option_cat = 'telegram' AND
		options.user_id = 99 AND
		options.option_key = 'session' AND
		options.option_value = '99'
		LIMIT 1";
		$result = \lib\db::get($get_sesstion);
		$result = \lib\utility\filter::meta_decode($result, null, ['return_object' => true])[0];
		handle::send_log(["tmp0" => $result['option_cat']]);
		handle::send_log(["tmp1" => $result]);
		if($get_back_response)
		{
			$response = $get_back_response;
			$text = '_'.$response['result']['text'].' (expired)_';
			$edit_return = [
				"text" 						=> $text,
				"chat_id" 					=> $response['result']['chat']['id'],
				"message_id" 				=> $response['result']['message_id']
				];
		}
		$get = self::check();
		if(!is_null($_value) && !$get)
		{
			return self::set($_value['lang'], ["ref" => "start_link"]);
		}
		elseif(!$get)
		{
			$inline_keyboard = array();
			$inline_keyboard[0][0] = [
			'text' => "فارسی",
			'callback_data' => "language/fa"
			];
			$inline_keyboard[0][1] = [
			'text' => "English",
			'callback_data' => "language/en"
			];
			$return = [
			"text" => T_("Please select your language"),
			"reply_markup" => ["inline_keyboard" => $inline_keyboard],
			"response_callback" => function($_response)
			{
				if($_response['ok'])
				{
					session::set('tmp', 'language_inline', $_response);
					session::set('tmp', 'expire', $_response);
				}
			}
			];
			if($edit_return)
			{
				bot::sendResponse($return);
				return callback_query::edit_message($edit_return, true);
			}
			callback_query::edit_message($return);
			return $return;
		}
		return false;
	}

	public static function check()
	{
		if(!self::$user_language)
		{
			self::$user_language = \lib\db\users::get_language(bot::$user_id);
		}
		return self::$user_language;
	}

	public static function set($_language, $_options = [])
	{
		foreach (self::$valid_lang as $key => $value) {
			$language = strtolower($_language);
			if(array_search($language, $value) !== false)
			{
				$options = ['update_on_duplicate' => false];
				$options['user_id'] = bot::$user_id;
				$meta = ["instert_text" => $_language];
				$options['option_meta'] = json_encode(array_merge($meta, $_options));
				self::$user_language = $key;
				return \lib\db\users::set_language($key, $options);
			}
		}
	}
}
?>