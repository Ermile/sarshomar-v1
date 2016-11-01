<?php
namespace content\saloos_tg\sarshomarbot\commands;
class poll_result
{
	public static function make($_poll_result, $_options = array())
	{
		$poll_result = $_poll_result;
		if(!$_poll_result)
		{
			return;
		}

		$step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
		self::add_message($message, "❔*" . html_entity_decode($poll_result['title']) . "*");
		preg_match("/^\\$\/([^\/]+)\/.*$/", $poll_result['url'], $url);
		$short_link_id = $url[1];
		$inline_keyboard = array();
		foreach ($poll_result['meta']['opt'] as $answer_key => $answer_value) {
			$callback_data = 'ask/poll/' . $short_link_id . '/' . $answer_key;
			if(array_key_exists("callback_data", $_options))
			{
				if(is_object($_options['callback_data']))
				{
					$callback_data = $_options['callback_data']($callback_data);
				}
				else
				{
					$callback_data = $_options['callback_data'] . "/" . $callback_data;
				}
			}
			$inline_keyboard[][0] = [
			'text' => $step_shape[$answer_key+1] .' '. $answer_value['txt'],
			'callback_data' => $callback_data
			];
			self::add_message($message, $step_shape[$answer_key+1] .' '. html_entity_decode($answer_value['txt']));
		}
		$inline_keyboard[][0] = [
		'text' => "❌ مایل به پاسخگویی نیستم",
		'callback_data' => 'cancel/$/' . $short_link_id. '/0'
		];
		self::add_message($message, "#sarshomar");
		$short_url = 'https://sarshomar.com/sp_' . $short_link_id;
		self::add_message($message, preg_replace("[_]", "\_", $short_url));
		// $short_url = '(https://telegram.me/SarshomarBot?start=sp_' . $short_link_id .')';
		// self::add_message($message, '[sp_'.$short_link_id.']' . preg_replace("[_]", "\_", $short_url));
		return ["message" => $message, "inline_keyboard" => $inline_keyboard];
	}

	public static function add_message(&$_array, $_message, $_index = null)
	{
		if(!is_array($_array))
		{
			$_array = array();
		}
		if($_index)
		{
			array_splice($_array, $_index, 0, $_message);
		}
		else
		{
			$_array[] = $_message;
		}
	}

	public static function get_message($_message)
	{
		return join("\n", $_message);
	}
}
?>