<?php
namespace content\saloos_tg\sarshomarbot\commands;
use \content\saloos_tg\sarshomarbot\commands\handle;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;

class utility
{
	public static function response_expire($_key, $_options = [])
	{
		$options = array();
		$options["on_expire"] = $_options;
		$options["key"] = $_key;
		return [function($_response, $_data, $_options)
		{
			if($_response->ok)
			{
				$on_expire = [
					"method" 					=> "editMessageText",
					"text" 						=> $_data['text'],
					"chat_id" 					=> $_response->result->chat->id,
					"message_id" 				=> $_response->result->message_id,
					'parse_mode' 				=> 'Markdown',
					'disable_web_page_preview' 	=> true
				];
				$_response->save_unique_id = time() . rand(123456, 999999);
				$_response->on_expire = array_merge($on_expire, $_options['on_expire']);
				if(isset($_options['after_ok']))
				{
					$after_ok = $_options['after_ok'];
					if(is_object($after_ok)){
						$after_ok($edit_return);
					}
					elseif(is_array($after_ok))
					{
						$after_ok[0]($_response, $_data, array_slice($after_ok, 1));
					}
				}
				session::set('expire', 'inline_cache', $_options['key'], $_response);
			}
		}, $options];
	}

	public static function inline($_text, $_callback)
	{
		return [
		"text" => $_text,
		"callback_data" => $_callback
		];
	}

	public static function object_to_array($_object)
	{
		$object = $_object;
		if(is_object($_object))
		{
			$object = (array) $_object;
		}
		foreach ($object as $key => $value) {
			if((is_object($value) || is_array($value)) && !is_callable($value))
			{
				$object[$key] = self::object_to_array($value);
			}
		}
		return $object;
	}

	public static function microtime_id($_pref = 'id_')
	{
		return $_pref . preg_replace(".", "_", microtime(true));
	}
}