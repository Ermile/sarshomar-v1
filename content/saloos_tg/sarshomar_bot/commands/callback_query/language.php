<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\menu;
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
		if(!$get || empty($get) || isset($_data_url[2]))
		{
			self::set($_data_url[1], ["ref" => "callback_query"]);
			$lang_name = $_data_url[1];
			$lang = $lang_name;
		}
		else
		{
			$lang_name = $get;
			$lang = $lang_name;
		}

		if(!$get)
		{
			$count = \saloos::lib_static('db')->users()::get_count('all');
			$text = T_("Welcome to the society of :count people of sarshomar",
				['count'=> utility::nubmer_language($count)]);
		}
		else
		{
			$text = T_("Welcome");
		}
		bot::sendResponse([
			"text" => $text,
			"reply_markup" => menu::main(true)
			]);
		$run = (array) session::get('step', 'run');
		if($run)
		{
			session::remove('step', 'run');
			if($run['text'] !== '/start')
			{
				bot::$cmd = $run;
				bot::sendResponse(handle::exec($run, true));
			}
		}
		callback_query::edit_message([
			'text' => T_("For changing language go to profile or enter /language"),
			]);
		session::remove_back('expire', 'inline_cache', 'language');
		session::remove('expire', 'inline_cache', 'language');
		return ['text' => '🗣 ' . T_("Your language set to :language ", ['language'=> $lang_name])];
	}

	public static function make_result($_value, $_update_on = false)
	{
		$return = false;
		$edit_return = false;
		$get = self::check();
		if(!is_null($_value) && !$get)
		{
			return self::set($_value, ["ref" => "start_link"]);
		}
		elseif(!$get || $_update_on)
		{
			$update_on = $_update_on ? '/update' : '';
			$inline_keyboard = array();
			$inline_keyboard[0][0] = [
			'text' => "فارسی",
			'callback_data' => "language/fa" . $update_on
			];
			$inline_keyboard[0][1] = [
			'text' => "English",
			'callback_data' => "language/en" . $update_on
			];
			$return = [
			"text" => T_("Select your language"),
			"reply_markup" => ["inline_keyboard" => $inline_keyboard],
			"response_callback" => utility::response_expire('language')
			];
			return $return;
		}
		return false;
	}

	public static function check($_min = false)
	{
		if(!self::$user_language)
		{
			$language = \lib\db\users::get_language(bot::$user_id);
			if(empty($language))
			{
				$language = null;
			}
			else
			{
				self::$user_language = $language;
			}
		}
		if($_min)
		{
			return preg_replace("/_.*$/", "", self::$user_language);
		}
		return self::$user_language;
	}

	public static function set_client_language()
	{
		\lib\define::set_language(self::check(true), true);
	}

	public static function set($_language, $_options = [])
	{
		$language = mb_strtolower($_language);
		foreach (self::$valid_lang as $key => $value) {
			if(array_search($language, $value) !== false)
			{
				step::stop();
				$options = ['update_on_duplicate' => true];
				$options['user_id'] = bot::$user_id;
				$meta = ["instert_text" => $_language];
				$options['option_meta'] = json_encode(array_merge($meta, $_options));
				self::$user_language = $key;
				\lib\define::set_language($key, true);
				return \lib\db\users::set_language($key, $options);
			}
		}
	}
}
?>