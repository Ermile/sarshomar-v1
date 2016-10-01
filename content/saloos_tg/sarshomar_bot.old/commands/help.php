<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class help
{
	/**
	 * show help message
	 * @return [type] [description]
	 */
	public static function help()
	{
		$text = "*_fullName_*\r\n\n";
		$text .= "دستورات زیر برای کار با ربات سرشمار در دسترس شماست:\r\n\n";
		$text .= "/sarshomar شروع نظرسنجی‌های سرشمار\n";
		$text .= "/psychology `تست‌های روانشناسی`\n";
		$text .= "/civility `نظرسنجی‌های مردمی`\n";
		// $text .= "/menu show main menu\n";
		$text .= "/my نظرسنجی‌های من\n";
		$text .= "/define `تعریف نظرسنجی جدید`\n";
		// $text .= "/polls مشاهده لیست نظرسنجی‌ها\n";
		$text .= "/profile تکمیل پروفایل\n";
		$text .= "/contact تماس با ما\n";
		$text .= "/about درباره _name_\n";
		$text .= "/cancel انصراف و شروع دوباره\n";
		$result =
		[
			[
				'text' => $text,
			],
		];

		return $result;
	}
}
?>