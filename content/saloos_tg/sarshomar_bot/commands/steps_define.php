<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class steps_define
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		steps::start('define');
		// define
		$txt_text = "مرحله ۱\n\n";
		$txt_text .= "برای تعریف نظرسنجی جدید در ابتدا سوال خود را وارد کنید.";
		$menu     =
		[
			"hide_keyboard" => true,
			"force_reply"   => true
		];

		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	public static function step1($_text)
	{
		// get name of question
		$result   =
		[
			[
				'text'         => $_text,
				// 'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	public static function end()
	{
		steps::stop();
	}
}
?>