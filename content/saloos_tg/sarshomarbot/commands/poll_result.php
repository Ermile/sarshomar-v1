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
		preg_match("/^\\$\/([^\/]+)(\/.*)*$/", $poll_result['url'], $url);
		$short_link_id = $url[1];
		$short_url = 'https://sarshomar.com/sp_' . $short_link_id;
		self::add_message($message, "[" . html_entity_decode($poll_result['title']) . "]($short_url)");
		$inline_keyboard = array();
		$keyboard_map = [
			1 => [
				[0, 0],
			],
			2 => [
				[0, 0] , [0, 1],
			],
			3 => [
				[0, 0] , [0, 1], [0, 2],
			],
			4 => [
				[0, 0] , [0, 1], [0, 2], [0, 3],
			],
			5 => [
				[0, 0] , [0, 1], [0, 2], [1, 0], [1, 1],
			],
			6 => [
				[0, 0] , [0, 1], [0, 2], [1, 0], [1, 1], [1, 2],
			],
			7 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2],
			],
			8 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2], [1, 3]
			],
			9 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2], [2, 0], [2, 1], [2, 2],
			],
		];
		$count_answer = count($poll_result['meta']['opt']);
		$row_answer = current($keyboard_map[$count_answer]);
		foreach ($poll_result['meta']['opt'] as $answer_key => $answer_value) {
			$callback_data = 'ask/poll/' . $short_link_id . '/' . ($answer_key +1);
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
			$inline_keyboard[$row_answer[0]][$row_answer[1]] = [
			'text' => $step_shape[$answer_key+1],
			'callback_data' => $callback_data
			];
			$row_answer = next($keyboard_map[$count_answer]);
			self::add_message($message, $step_shape[$answer_key+1] .' '. html_entity_decode($answer_value['txt']));
		}
		$inline_keyboard[] = [
			[
			'text' => "پرش",
			'callback_data' => 'ask/poll/' . $short_link_id. '/0'
			],
			[
			"text" => T_("Share"),
			"switch_inline_query" => 'sp_'.$short_link_id
			]
		];
		self::add_message($message, '['.T_('Answer link').']' . "(https://telegram.me/SarshomarBot?start=sp_$short_link_id)");
		self::add_message($message, "#sarshomar");
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