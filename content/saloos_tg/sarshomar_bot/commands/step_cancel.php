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

class step_cancel
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('cancel');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$make = new make_view(session::get('poll'));
		$make->message->add_title();
		$make->message->add_poll_list();
		$make->message->add('alert', "\n✳ " . T_("This poll has been drafted."));
		$make->message->add('tag', utility::tag(T_("Cancel")));
		$make->inline_keyboard->add([
			[
				"text" => T_("Save"),
				"callback_data" => 'create/save',
			],
			[
				"text" => T_("Delete"),
				"callback_data" => 'create/delete'
			]
		]);
		$return = $make->make();
		$return["response_callback"] = utility::response_expire('cancel');
		return $return;
	}
	public static function step2($_save = false)
	{
		$poll_id = session::get('poll');
		session::remove('poll');
		if(!$_save)
		{
			utility::make_request(["id" => $poll_id, "status" => 'trash']);
			\lib\main::$controller->model()->poll_set_status();

			if(debug::$status === 0) return self::error();
		}
		$return = [];
		$return['text'] = T_("Main menu");
		$return['method'] = 'sendMessage';
		$return['reply_markup'] = menu::main(true);
		bot::sendResponse($return);
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