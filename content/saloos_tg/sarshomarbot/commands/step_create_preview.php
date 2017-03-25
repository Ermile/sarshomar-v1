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

class step_create_preview
{
	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_preview');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();
		$maker->message->add_poll_list(null, false);
		if($maker->poll_type == 'emoji')
		{
			$maker->message->add('emoji', join('/', array_column($maker->query_result['answers'], 'title')));
		}

		$maker->message->add('publish',"\n✅ " . T_("publish your poll by press publish."));
		$maker->inline_keyboard->add([
			[
				"text" => '✅ ' . T_("Publish"),
				"callback_data" => 'poll/status/publish/'.$poll_id
			]
		]);

		$maker->message->add('advance', '⚛ ' . T_("if wanna more options, press advance."));
		$maker->inline_keyboard->add([
				[
					"text" => '⚛ ' . T_("Advanced"),
					"callback_data" => 'create/advance'
				]
			]);
		$maker->message->add('tag', utility::tag(T_('Preview')));
		$maker->inline_keyboard->add([
			[
				"text" => T_("Cancel"),
				"callback_data" => 'create/cancel'
			]
		]);
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}
}
?>