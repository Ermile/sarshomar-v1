<?php
namespace content\saloos_tg\sarshomarbot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\markdown_filter;
use \content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\main;
use \lib\debug;

class step_create_select
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_select');
		return self::step1();
	}


	public static function step1($_text = null, $_error = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();
		if($_text)
		{
			$answers = explode("\n", $_text);
			foreach ($answers as $key => $value) {
				if(empty($value) || $value == "" || !$value)
				{
					unset($maker->query_result['answers'][$key]);
					continue;
				}
				$maker->query_result['answers'][] = [
				"key" => count($maker->query_result['answers']) + 1,
				"type" => "select",
				"title" => $value,
				];
			}
			$check_row = 1;
			foreach ($maker->query_result['answers'] as $key => $value) {
				$maker->query_result['answers'][$key]['key'] = $check_row++;
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



		if($count_answer < 2)
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

		if(is_string($_error))
		{
			$make->message->add('error', "\n" . "⭕ " . $_error);
		}

		$maker->message->add('tag', utility::tag(T_("Create new poll")));

		if($_text)
		{
			$answers = [];
			foreach ($maker->query_result['answers'] as $key => $value) {
				$answers[] = ['type' => 'select', 'title' => $value['title']];
			}

			utility::make_request(['id' => $poll_id, 'answers' => $answers]);
			main::$controller->model()->poll_add(['method' => 'patch']);

			if(debug::$status === 0)
			{
				return self::step1(null, self::error());
			}
		}

		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function error()
	{
		debug::$status = 1;
		return debug::compile()['messages']['error'][0]['title'];
	}
}
?>