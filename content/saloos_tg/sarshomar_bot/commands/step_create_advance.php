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

class step_create_advance
{
	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_advance');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();

		$maker->message->add('desc', "\n".T_("شما می‌توانید انواعت تنظیمات دلخواه خود را برای این سوال اعمال کنید"));

		$maker->inline_keyboard->add([
			[
				'text' => T_('گزینه‌ها'),
				"callback_data" => 'create_advance/anwers'
			],
			[
				'text' => T_('Description'),
				"callback_data" => 'create_advance/description'
			]
			]);
		$maker->inline_keyboard->add([
			[
				'text' => T_('Privacy'),
				"callback_data" => 'create_advance/privacy'
			]
			]);
		$maker->inline_keyboard->add([
			[
				'text' => T_('Cancel'),
				"callback_data" => 'create/cancel'
			]
			]);
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function step2($substep = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_poll_list(null, false);

		$maker->inline_keyboard->add([
			[
				'text' => T_('حذف گزینه‌ها'),
				"callback_data" => 'create_advance/delete'
			]
			]);

		$maker->inline_keyboard->add([
			[
				'text' => T_('Cancel'),
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