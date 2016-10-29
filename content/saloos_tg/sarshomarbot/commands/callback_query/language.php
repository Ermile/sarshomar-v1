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
		bot::sendResponse([
			"method" => "sendMessage",
			"text" => T_("Welcome"),
			"reply_markup" => menu::main(true)
			]);
		callback_query::edit_message([
			'text' => 'Language set '. $lang
			]);
		session::remove_back('expire', 'inline_cache', 'language');
		session::remove('expire', 'inline_cache', 'language');
		return ['text' => '🗣 Your language set : ' . $lang_name];
	}

	public static function make_result($_value)
	{
		$return = false;
		$edit_return = false;
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
			"response_callback" => utility::response_expire('language')
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