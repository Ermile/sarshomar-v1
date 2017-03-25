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


	public static function step1($_text = null, $_error = [])
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();
		$duplicate = [];
		$error = !empty($_error) ? $_error : [];
		if($_text)
		{
			$answers = preg_split("/[\n\s]/", $_text);
			$duplicate_error = [];
			foreach ($answers as $key => $value) {
				if(in_array($value, $duplicate_error) || empty($value) || $value == "" || !$value)
				{
					continue;
				}
				if(!\lib\utility\emoji::check($value))
				{
					$error[] = T_("Please insert emoji.");
					$duplicate_error[] = $value;
					continue;
				}
				if(in_array($value, $duplicate))
				{
					continue;
				}
				$duplicate[] = $value;
			}
			if($error)
			{
				return self::step1(null, $error);
			}
		}
		elseif(!empty($maker->query_result['answers']))
		{
			foreach ($maker->query_result['answers'] as $key => $value) {
				$duplicate[] = $value['title'];
			}
		}
		sort($duplicate);
		$maker->message->add_poll_list(null, false);
		$maker->message->add('emoji', join('/', $duplicate));
		$count = ['first', 'second', 'third'];
		$count_answer = count($maker->query_result['answers']);

		if(!empty($error))
		{
			$maker->message->add('error', "\n🚫 " . join("\n🚫 ", $error));
		}

		$maker->message->add('insert', "\n📍 ". T_("seperate emoji by space and send them."));
		if(!is_null($maker->query_result['answers'][0]['title']) || $_text != null)
		{
			$maker->message->add('insert2', "📍📍 ". T_("by press preview, start publish process or change emoji list."));
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

		if(!empty($duplicate) && empty($error))
		{
			$answers = [];
			foreach ($duplicate as $key => $value) {
				$answers[] = ['type' => 'emoji', 'title' => $value];
			}

			utility::make_request(['id' => $poll_id, 'answers' => $answers]);
			main::$controller->model()->poll_add(['method' => 'patch']);

			if(debug::$status === 0)
			{
				return self::step1(null, [self::error()]);
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