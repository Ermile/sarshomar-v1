<?php
namespace content\saloos_tg\sarshomar_bot\commands;

use \lib\telegram\tg as bot;

class spammer
{
	public static $on_spam = '';
	public static function check()
	{
		$valid = ['message', 'inline_query','callback_query'];
		$on_spam = array_intersect($valid, array_keys(bot::$hook));
		if(empty($on_spam))
		{
			return false;
		}


		$on_spam = self::$on_spam = current($on_spam);

		$get_count_log = \lib\db\options::get([
			"option_cat" => "user_detail_" . bot::$user_id,
			"option_key" => "telegram",
			"option_value" => "acction_log",
			"limit"	=> 1
			]);
		if(empty($get_count_log))
		{
			$set_meta = ['count' => 0, "time" => microtime(true)];
			\lib\db\options::insert([
			"option_cat" => "user_detail_" . bot::$user_id,
			"option_key" => "telegram",
			"option_value" => "acction_log",
			"option_meta" => self::set_meta($set_meta)
 			]);
 			return false;
		}

		$meta = self::get_meta($get_count_log['meta']);

		if(isset($meta['deny_time']))
		{
			if($meta['deny_time'] + 60 < microtime(true))
			{
				\lib\db\options::update([
				"option_meta" => self::set_meta(['count' => 0, 'time' => microtime(true)])
	 			], $get_count_log['id']);
			}
			else
			{
				if($on_spam == 'callback_query')
				{
					return [
						'method'=> "answerCallbackQuery",
						'callback_query_id' => bot::$hook['callback_query']['id']
					];
				}
				return true;
			}
			return false;
		}
		if(isset($meta['text']) && $on_spam == 'message' && isset(bot::$hook['message']['text']))
		{
			$md5_msg = md5(bot::$hook['message']['text']);
			if($meta['text'] == $md5_msg && $meta['text_time'] + 5 > microtime(true))
			{
				return true;
			}
		}

		$overflow = self::{"overflow_" . $on_spam}($meta);
		if($overflow)
		{
			handle::send_log($overflow);
			\lib\db\options::update([
			"option_meta" => self::set_meta(['deny_time' => microtime(true)])
 			], $get_count_log['id']);
			return $overflow;
		}

		if($meta['time'] + 10 < microtime(true))
		{
			$meta['time'] = microtime(true);
		}

		\lib\db\options::update([
			"option_meta" => self::set_meta(['count' => ++$meta['count'], "time" => $meta['time']])
 			], $get_count_log['id']);

		return false;
	}

	public static function overflow_message($_meta)
	{
		if($_meta['time'] + 40 >= microtime(true) && $_meta['count'] >= 20)
		{
			return [
			'text' => T_("You are banned for :seconds seconds", ['seconds' => 20]),
			"reply_markup" => menu::main(true),
			];
		}
		return false;
	}

	public static function overflow_inline_query($_meta)
	{
		// return ['text' => T_("You are banned for :seconds seconds", ['seconds' => 20])];
		return false;

	}

	public static function overflow_callback_query($_meta)
	{
		if($_meta['time'] + 15 >= microtime(true) && $_meta['count'] >= 4)
		{
			$message_result = [
				'method'=> "answerCallbackQuery",
				'text' => T_("You are banned for :seconds seconds", ['seconds' => 20]),
				'callback_query_id' => bot::$hook['callback_query']['id']
			];
			bot::sendResponse([
				"method" => "sendMessage",
				"text" => T_("You are banned for :seconds seconds", ['seconds' => 20]),
				"reply_markup" => menu::main(true)
				]);
			return $message_result;
		}
		return false;

	}

	public static function get_meta($_meta)
	{
		$meta = [];
		$property = explode(",", $_meta);
		foreach ($property as $key => $value) {
			$var = explode("=", $value, 2);
			if(count($var) == 2)
			{
				$meta[$var[0]] = $var[1];
			}
			else
			{
				$meta[$var[0]] = null;
			}
		}
		return $meta;
	}

	public static function set_meta($_meta)
	{
		if(self::$on_spam == 'message' && isset(bot::$hook['message']['text']) && !isset($_meta['text']))
		{
			$_meta['text'] = md5(bot::$hook['message']['text']);
			$_meta['text_time'] = microtime(true);
		}
		elseif(isset(bot::$hook['message']['text']) && isset($_meta['text']))
		{
			if($_meta['text'] == md5(bot::$hook['message']['text']))
			{
				$_meta['text'] = $_meta['text'];
				$_meta['text_time'] = $_meta['text_time'];
			}
			else
			{
				$_meta['text'] = md5(bot::$hook['message']['text']);
				$_meta['text_time'] = microtime(true);
			}
		}
		$meta = [];
		foreach ($_meta as $key => $value) {
			$meta[] = "$key=$value";
		}
		return join(',', $meta);
	}
}