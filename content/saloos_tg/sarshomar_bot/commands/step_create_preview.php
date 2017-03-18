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

		if($maker->query_result['description'])
		{
			$maker->message->add('description', "📝 " . $maker->query_result['description']);
		}


		$maker->message->add('publish',"\n✅ " . T_("با فشردن دکمه انتشار، سوال خود را منتشر کنید."));
		$maker->inline_keyboard->add([
			[
				"text" => '✅ ' . T_("Publish"),
				"callback_data" => 'poll/status/publish/'.$poll_id
			]
		]);

		$maker->message->add('advance', '⚛ ' . T_("در صورت نیاز به اعمال ویژگی‌های بیشتر، دکمه پیشرفته را بفشارید."));
		$maker->inline_keyboard->add([
				[
					"text" => '⚛ ' . T_("Advance"),
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