<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class menu
{
	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function main($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["شرکت در نظرسنجی"],
				["نظرسنجی‌های من"],
				["پروفایل"],
			],
		];

		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "منوی اصلی\n\n";

		$result =
		[
			[
				// 'method'       => 'editMessageReplyMarkup',
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	/**
	 * create polls menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function polls($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["نظرسنجی‌های سرشمار"],
				["مردمی", "روانشناسی"],
				["بازگشت به منوی اصلی"],
			],
			// "one_time_keyboard" => true,
			"force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "*_fullName_*\r\n\n";
		$txt_text .= "لطفا از منوی زیر یکی از انواع نظرسنجی‌ها را انتخاب نمایید";
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
}
?>