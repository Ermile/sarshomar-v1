<?php
namespace content\saloos_tg\sarshomar_bot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;
use \lib\main;
use \lib\debug;

class step_create_emoji
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_select');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();
		$duplicate = [];
		foreach ($maker->query_result['answers'] as $key => $value) {
			if($value['title'] == "" || is_null($value['title']))
			{
				unset($maker->query_result['answers'][$key]);
			}
			$duplicate[] = $value['title'];
		}
		$error = [];
		if($_text)
		{
			$answers = preg_split("/[\n\s]/", $_text);
			$duplicate_error = [];
			foreach ($answers as $key => $value) {
				if(in_array($value, $duplicate_error))
				{
					continue;
				}
				$lValue = preg_replace("/[️‍]/‍", "", $value);
				if(empty($value) || $value == "" || !$value || mb_strlen($lValue) > 4)
				{
					$error[] = T_("متن ایموجی باید بین ۱ تا ۴ کاراکتر باشد");
					$duplicate_error[] = $value;
					continue;
				}
				if(in_array($value, $duplicate))
				{
					$duplicate_error[] = $value;
					$error[] = T_("این ایموجی موجود است") . " - $value";
					continue;
				}
				$duplicate[] = $value;
				$maker->query_result['answers'][] = [
				"key" => count($maker->query_result['answers']) + 1,
				"type" => "emoji",
				"title" => $value,
				];
			}
		}
		$maker->message->add_poll_list(null, false);
		$count = ['first', 'second', 'third'];
		$count_answer = count($maker->query_result['answers']);
		if($count_answer > 2)
		{
			if($count_answer > 20 && substr($count_answer, -1) == 0)
			{
				$count = ($count_answer +1) .'st';
			}
			elseif($count_answer > 20 && substr($count_answer, -1) == 1)
			{
				$count = ($count_answer +1) .'nd';
			}
			elseif($count_answer > 20 && substr($count_answer, -1) == 2)
			{
				$count = ($count_answer +1) .'rd';
			}
			else
			{
				$count = ($count_answer +1) .'th';
			}
		}
		else
		{
			$count = $count[$count_answer];
		}

		if(!empty($error))
		{
			$maker->message->add('error', "🚫 " . join("\n🚫 ", $error));
		}
		if($count_answer < 1)
		{
			$maker->message->add('insert', "📍 ". T_("Enter the text of :count option", ['count' => $count]));
			$maker->message->add('alert', "\n✳ " . T_("enter at least two option is nessecary"));
		}
		else
		{
			$maker->message->add('insert', "📍 ". T_("by press preview, start publish process or enter option :count", ['count' => $count]));
			$maker->inline_keyboard->add([
				[
					"text" => T_("Preview"),
					"callback_data" => 'create/preview',
				]
			]);
		}

		$maker->inline_keyboard->add([
			[
				"text" => T_("Cancel"),
				"callback_data" => 'create/cancel',
			]
		]);
		$maker->message->add('tag', utility::tag(T_("Create new poll")));

		if($_text)
		{
			$answers = [];
			foreach ($maker->query_result['answers'] as $key => $value) {
				$answers[] = ['type' => 'emoji', 'title' => $value['title']];
			}

			utility::make_request(['id' => $poll_id, 'answers' => $answers]);
			main::$controller->model()->poll_add(['method' => 'patch']);

			if(debug::$status === 0) return self::error();
		}

		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function error()
	{
		debug::$status = 1;
		step::stop();
		return [
			'text' => debug::compile()['messages']['error'][0]['title'],
			'reply_markup' => menu::main(true)
		];
	}
}
?>