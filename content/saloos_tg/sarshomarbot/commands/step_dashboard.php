<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;

class step_dashboard
{
	private static $menu = ["hide_keyboard" => true];

	public static function start($_text = null)
	{
		return [
		"text"			=> "please selecet your menue",
		"reply_markup"	=> [
			'keyboard' =>
			[
				[T_('Profile'), T_('Language')],
				[[
					'text' 				=> T_('Register & sync'),
					'request_contact' 	=> true
				]],
				[T_('Back')]
			],
			"resize_keyboard" => true
			]
		];
	}
}
?>