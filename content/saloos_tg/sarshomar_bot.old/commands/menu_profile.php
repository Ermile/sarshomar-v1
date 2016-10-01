<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class menu_profile
{
	/**
	 * create profile menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function profile($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				// get other detail as soon as posible
				// ["تکمیل پروفایل"],
				[
					[
						'text'             => 'ثبت آدرس',
						'request_location' => true
					],
					[
						'text'            => 'ثبت موبایل',
						'request_contact' => true
					],
				],
				["بازگشت به منوی اصلی"],
			],
			// "one_time_keyboard" => true,
			// "force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "*پروفایل*\r\n\n";
		$txt_text .= "با تکمیل پروفایل خود";
		$txt_text .= "به ما در افزایش اعتبار نتایج نظرسنجی‌ها کمک کنید.\n";
		$txt_text .= "ما نیز در حد توان خود از این اقدام شما سپاسگذاری خواهیم کرد.";
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
	 * [profile description]
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function getContact($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				[
					[
						'text'            => 'ثبت مخاطب',
						'request_contact' => true
					],
				],
				["بازگشت به منوی اصلی"],
			],
			// "one_time_keyboard" => true,
			// "force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "*پروفایل*\r\n\n";
		$txt_text .= "با تکمیل پروفایل خود";
		$txt_text .= "به ما در افزایش اعتبار نتایج نظرسنجی‌ها کمک کنید.\n";
		$txt_text .= "ما نیز در حد توان خود از این اقدام شما سپاسگذاری خواهیم کرد.";
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