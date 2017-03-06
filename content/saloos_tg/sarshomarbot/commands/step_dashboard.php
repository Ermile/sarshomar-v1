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
		$text = T_("Now you have entered your dashboard. Here, you can change the language or observe your profile.");
		$text .= "\n";
		$text .= T_("Besides, you can complete your registration and account integration on the website and telegram by clicking on the register button and synchronization of your cell phone number.");
		return [
		"text"			=> $text,
		"reply_markup"	=> [
			'keyboard' =>
			[
				// [T_('My polls')],
				// [T_('Profile'), T_('Language')],
				// [T_('Back'), T_('Help')],
				[T_('My polls')],
				[T_('Help'), T_('Language')],
				[T_('Back')],
			],
			"resize_keyboard" => true
			]
		];
	}

	public static function profile(){
		$profile = \lib\utility\profiles::get_profile_data(bot::$user_id, false);
		$text = T_('Your info:');
		foreach ($profile as $key => $value) {
			$text .= "\n";
			$text .= T_($key) . ': '. $value;
		}
		$text .= "\n";
		$text .= "#" . T_("Profile");
		return ['text' => $text, 'parse_mode' => 'html'];
	}
}
?>