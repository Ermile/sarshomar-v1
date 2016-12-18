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
		$poll_emoji = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
		$count = array_sum($_result);
		$max = max($_result) == 0 ? 0 : (max($_result) * 100) / $count;
		$rows = 5;
		$result = array();
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
				array_push($row_text, ...array_fill(0, $rows - count($row_text), '⬜️'));
			}
			array_unshift($row_text, $poll_emoji[$key]);
			$result[$key]['row_text'] = $row_text;
		}
		$text = '';
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
		return $text;
	}

	public static function replay_markup_id(){
		return function(&$_name, &$_args){
			if(isset($_args['reply_markup']) && isset($_args['reply_markup']['inline_keyboard']))
			{
				$session_id = self::markup_set_id($_args['reply_markup']['inline_keyboard']);
				if(!isset($_args['storage']) || !is_array($_args['storage']))
				{
					$_args['storage'] = array();
				}
				$_args['storage']['callback_session'] = $session_id;

			}
			elseif(isset($_args['results']))
			{
				foreach ($_args['results'] as $key => $value) {
					if(isset($_args['results'][$key]['reply_markup']) && isset($_args['results'][$key]['reply_markup']['inline_keyboard']))
					{
						$session_id = self::markup_set_id($_args['results'][$key]['reply_markup']['inline_keyboard']);
						if(!isset($_args['storage']) || !is_array($_args['storage']))
						{
							$_args['storage'] = array();
						}
						$_args['storage']['callback_session'] = $session_id;
					}
				}
			}
		};
	}

	public static function markup_set_id(&$reply_markup)
	{
		$id = preg_replace("[\.]", '_', microtime(true));
		$callback_session = (array) session::get('tmp', 'callback_query');
		if(!is_array($callback_session))
		{
			$callback_session = [];
		}
		$session_id = "ik_$id";
		$callback_session[$session_id] = true;

		session::set('tmp', 'callback_query', $callback_session);

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
			T_(html_entity_decode($_title)) . '</a>';
	}

	public static function italic($_text)
	{
		return '<i>' . $_text . '</i>';
	}
}