<?php
namespace content\saloos_tg\sarshomar_bot\commands;
use \content\saloos_tg\sarshomar_bot\commands\handle;
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
				'parse_mode' 				=> 'HTML',
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
		return $_pref . preg_replace("[\.]", "_", microtime(true));
	}

	public static function calc_vertical($_result)
	{
		if(empty($_result))
		{
			return "";
		}
		$poll_emoji = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
		$count = array_sum($_result);
		$max = max($_result) == 0 ? 0 : (max($_result) * 100) / $count;
		$rows = 5;
		$result = array();
		$max_key =max(array_keys($_result));
		foreach ($_result as $key => $value) {
			$value = $value == 0 ? 0 : ($value * 100) / $count;
			$result[$key]['percent'] = $value;
			$decimal = $value == 0 ? 0 : $value / 20;
			$row_text = array_fill(0, $decimal, '⬛️');
			$under_decimal = $decimal - floor($decimal);
			if($under_decimal > 0.5)
			{
				array_push($row_text, '🔲');
			}
			elseif($under_decimal > 0)
			{
				array_push($row_text, '🔳');
			}
			if(count($row_text) < $rows)
			{
				// array_push($row_text, ...array_fill(0, $rows - count($row_text), '⬜️'));
			}

			if($max_key > 9)
			{
				$key_row = '';
				foreach (str_split($key) as $k => $v) {
					$key_row .= $poll_emoji[$v];
				}
				if($key == 0)
				{
					$key_row = "*️⃣*️⃣";
				}
				elseif($key < 9)
				{
					$key_row = $poll_emoji[0] . $key_row;
				}
			}
			else
			{
				if($key == 0)
				{
					$key_row = "*️⃣";
				}
				else
				{
					$key_row = $poll_emoji[$key];
				}
			}

			array_unshift($row_text, $key_row);
			$result[$key]['row_text'] = $row_text;
		}
		$text = '';
		if(true)
		{
			foreach ($result as $key => $value) {
				$text .= join($value['row_text']);
				$text .= ' ' . self::nubmer_language(round($value['percent']) ."%");
				if(end($result) != $value)
				{
					$text .= "\n";
				}
			}
		}
		else
		{
			for($row = $rows ; $row >= 0; $row--)
			{
				foreach ($result as $key => $value) {
					$text .= $value['row_text'][$row];
				}
				if($row > 0)
				{
					$text .= "\n";
				}
			}
		}

		return $text;
	}

	public static function replay_markup_id(){
		return function(&$_name, &$_args){
			if(isset($_args['reply_markup']) &&
				isset($_args['reply_markup']['inline_keyboard']) &&
				!isset($_args['inline_message_id'])
				)
			{

				$session_id = self::markup_set_id($_args['reply_markup']['inline_keyboard']);
				if(!isset($_args['storage']) || !is_array($_args['storage']))
				{
					$_args['storage'] = array();
				}
				$_args['storage']['callback_session'] = $session_id;

			}
			// elseif(isset($_args['results']))
			// {
			// 	foreach ($_args['results'] as $key => $value) {
			// 		if(isset($_args['results'][$key]['reply_markup']) && isset($_args['results'][$key]['reply_markup']['inline_keyboard']))
			// 		{
			// 			$session_id = self::markup_set_id($_args['results'][$key]['reply_markup']['inline_keyboard'], true);
			// 			if(!isset($_args['storage']) || !is_array($_args['storage']))
			// 			{
			// 				$_args['storage'] = array();
			// 			}
			// 			$_args['storage']['callback_session'] = $session_id;
			// 		}
			// 	}
			// }
		};
	}

	public static function markup_set_id(&$reply_markup, $_nosession = false)
	{
		$id = preg_replace("[\.]", '_', microtime(true));
		$callback_session = (array) session::get('tmp', 'callback_query');
		if(!is_array($callback_session))
		{
			$callback_session = [];
		}
		$session_id = "ik_$id";
		$callback_session[$session_id] = true;

		if(!$_nosession)
		{
			session::set('tmp', 'callback_query', $callback_session);
		}

		for ($i=0; $i < count($reply_markup); $i++)
		{
			for ($j=0; $j < count($reply_markup[$i]); $j++)
			{
				if(isset($reply_markup[$i][$j]['callback_data']))
				{
					$reply_markup[$i][$j]['callback_data'] = $id . ':' . $reply_markup[$i][$j]['callback_data'];
				}
			}
		}
		return $session_id;
	}

	public static function callback_session()
	{
		return function(&$_name, &$_args, $_return){
			if(isset($_return['ok']) && $_return['ok'] == true)
			{
				$log = ["telegram" => bot::$hook, "request" => $_args, "response" => $_return];
				\lib\db::log($log, null, 'telegram.json', 'json');
			}
			else
			{
				$log = ["telegram" => bot::$hook, "request" => $_args, "response" => $_return, "debug" => \lib\debug::compile()];
				\lib\db::log($log, null, 'telegram-error.json', 'json');
			}
			if(isset($_args['storage']) && isset($_args['storage']['callback_session']))
			{
				$callback_session = $_args['storage']['callback_session'];
				$callback_query = session::get('tmp', 'callback_query');
				if(isset($callback_query[$callback_session]))
				{
					if(!isset($_return['result']['message_id']))
					{
						unset($callback_query[$callback_session]);
					}
					else
					{
						$callback_query[$callback_session] = $_return['result']['message_id'];
					}
				}
				session::set('tmp', 'callback_query', $callback_query);
			}
		};
	}

	public static function utf8_validate($_str) {
    	$regex = <<<'END'
/^(?:
    [\x00-\x7F]
    | [\xC2-\xDF][\x80-\xBF]
    | \xE0[\xA0-\xBF][\x80-\xBF]
    | [\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}
    | \xED[\x80-\x9F][\x80-\xBF]
    | \xF0[\x90-\xBF][\x80-\xBF]
    | [\xF1-\xF3][\x80-\xBF]{3}
    | \xF4[\x80-\x8F][\x80-\xBF]{2}
)*$/x
END;
	    return preg_match($regex, $_str) === 1;
	}

	public static function emoji_validate($_str)
	{
    	static $regex = <<<'END'
/(?:
    [\x00-\x7F]
    | [\xC2-\xDF][\x80-\xBF]
    | \xE0[\xA0-\xBF][\x80-\xBF]
    | [\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}
    | \xED[\x80-\x9F][\x80-\xBF]
    | \xF0[\x90-\xBF][\x80-\xBF]
    | [\xF1-\xF3][\x80-\xBF]{3}
    | \xF4[\x80-\x8F][\x80-\xBF]{2}
)$/x
END;
    	return (preg_match($regex, $_str) === 1) === false;
	}

	public static function fa_number($_text)
	{
		return preg_replace_callback("/\d/", function($_str){
		    $fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
		    return $fa[$_str[0]];
		}, $_text);
	}

	public static function nubmer_language($_text)
	{
		$text = $_text;
		if(\lib\define::get_language() == 'fa')
		{
			$text = preg_replace_callback("/[\d%]/", function($_str){
				$fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '%' => '٪'];
				return $fa[$_str[0]];
			}, $text);
		}
		return $text;
	}

	public static function un_tag($_string)
	{
		$string = preg_replace("[_]", " ", $_string);
		return $string;
	}

	public static function tag($_string)
	{
		return "#" . ucfirst(preg_replace("[\s]", "_", $_string));
	}

	public static function link($_link, $_title)
	{
		return '<a href="' .$_link . '">' .
			T_($_title) . '</a>';
	}

	public static function italic($_text)
	{
		return '<i>' . $_text . '</i>';
	}
}