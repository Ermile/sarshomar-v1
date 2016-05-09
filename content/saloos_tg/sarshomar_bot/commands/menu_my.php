<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class menu_my
{
	/**
	 * create my menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function my($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["تعریف نظرسنجی جدید"],
				["نظرسنجی‌های موجود"],
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
		$txt_text .= "بخش نظرسنجی‌های مردمی به زودی راه‌اندازی خواهد شد";
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


	/**
	 * create my menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function mypolls($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["تعریف نظرسنجی جدید"],
				["نظرسنجی‌های موجود"],
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
		$txt_text .= "بزودی تکمیل خواهد شد...";
		$result   =
		[
			[
				'text'         => $txt_text,
				// 'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function define($_onlyMenu = false)
	{
		// define
		$menu =
		[
			"hide_keyboard" => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "مرحله ۱\n\n";
		$txt_text .= "برای تعریف نظرسنجی جدید در ابتدا سوال خود را وارد کنید.";
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