<?php
namespace content\saloos_tg\sarshomarbot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;

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
			// "user_id" => bot::$user_id,
			"option_cat" => "user_detail_" . bot::$user_id,
			"option_key" => "telegram",
			"option_value" => "action_log",
			"limit"	=> 1
			]);
		if(empty($get_count_log) || !$get_count_log)
		{
			$set_meta = [$on_spam . '_count' => 0, "time" => microtime(true)];
			\lib\db\options::insert([
			"user_id" => bot::$user_id,
			"option_cat" => "user_detail_" . bot::$user_id,
			"option_key" => "telegram",
			"option_value" => "action_log",
			"option_meta" => self::set_meta($set_meta)
 			]);
 			return false;
		}

		$meta = self::get_meta($get_count_log['meta']);
		if(isset($meta['time']))
		{
			$meta['time'] = floatval($meta['time']);
		}
		else
		{
			$meta['time'] = null;
		}
		if(isset($meta['deny_time']))
		{
			if($meta['deny_time'] + 20 < microtime(true))
			{
				\lib\db\options::update([
				"option_meta" => self::set_meta(['time' => microtime(true)])
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
			if(!isset(bot::$hook['callback_query']['inline_message_id']))
			{
				step::stop();
			}
			\lib\db\options::update([
			"option_meta" => self::set_meta(['deny_time' => microtime(true)])
 			], $get_count_log['id']);
			return $overflow;
		}

		if($on_spam == 'callback_query' && $meta['time'] + 5 < microtime(true))
		{
			$meta['time'] = microtime(true);
			$meta['callback_query_count'] = 0;
		}
		elseif($meta['time'] + 10 < microtime(true))
		{
			$meta['time'] = microtime(true);
			$meta['text_count'] = 0;
		}
		$set_meta = self::set_meta([
				$on_spam .'_count' => isset($meta[$on_spam .'_count']) ? ++$meta[$on_spam .'_count'] : 0,
				"time" => $meta['time']
				]);
		\lib\db\options::update([
			"option_meta" => $set_meta
 			], $get_count_log['id']);

		return false;
	}

	public static function overflow_message($_meta)
	{
		if(!isset($_meta['message_count']))
		{
			$_meta['message_count'] = 0;
		}
		if($_meta['time'] + 40 >= microtime(true) && $_meta['message_count'] >= 20)
		{
			return [
			'chat_id' => bot::response('from'),
			'text' => T_("You are banned for :seconds seconds", ['seconds' => utility::nubmer_language(20)]),
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
		if(!isset($_meta['callback_query_count']))
		{
			$_meta['callback_query_count'] = 0;
		}
		if($_meta['time'] + 5 >= microtime(true) && $_meta['callback_query_count'] >= 3)
		{
			$message_result = [
				'method'=> "answerCallbackQuery",
				"show_alert" => true,
				'text' => T_("You are banned for :seconds seconds", ['seconds' => utility::nubmer_language(20)]),
				'callback_query_id' => bot::$hook['callback_query']['id']
			];
			if(!isset(bot::$hook['callback_query']['inline_message_id']))
			{
				bot::sendResponse([
					'chat_id' => bot::response('from'),
					"method" => "sendMessage",
					"text" => T_("You are banned for :seconds seconds", ['seconds' => utility::nubmer_language(20)]),
					"reply_markup" => menu::main(true)
					]);
			}

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