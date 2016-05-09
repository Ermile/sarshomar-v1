<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class menu
{
	public static $return = false;

	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case '/menu':
			case '/cancel':
			case '/stop':
			case 'menu':
			case 'main':
			case 'mainmenu':
			case 'منو':
				$response = self::main();
				break;

			case '/poll':
			case 'poll':
			case '/polls':
			case 'polls':
			case 'شرکت':
			case 'شرکت در نظرسنجی':
				$response = self::polls();
				break;

			case '/sarshomar':
			case 'sarshomar':
			case 'نظرسنجی‌های':
			case 'نظرسنجی‌های سرشمار':
				$response = self::sarshomar();
				break;

			case '/psychology':
			case 'psychology':
			case 'روانشناسی':
				$response = self::psychology();
				break;

			case '/civility':
			case 'civility':
			case 'مردمی':
				$response = self::civility();
				break;

			case '/standard':
			case 'standard':
			case 'استاندارد':
				$response = self::room_standard();
				break;

			case '/modern':
			case 'modern':
			case 'مدرن':
				$response = self::room_modern();
				break;

			case '/family':
			case 'family':
			case 'خانواده':
				$response = self::room_family();
				break;

			case '/lux':
			case 'lux':
			case 'مجلل':
				$response = self::room_lux();
				break;


			case 'loc':
			case 'موقعیت':
				$response = self::menu_loc();
				break;


			case 'inline':
			case 'اینلاین':
				$response = self::menu_inline();
				break;

			case 'return':
			case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی اصلی':
						$response = self::main();
						break;

					case 'بازگشت به منوی نظرسنجی‌ها':
						$response = self::polls();
						break;

					default:
						$response = self::main();
						break;
				}
				break;

			default:
				break;
		}

		// automatically add return to end of keyboard
		if(self::$return)
		{
			// if has keyboard
			if(isset($response['replyMarkup']['keyboard']))
			{
				$response['replyMarkup']['keyboard'][] = ['بازگشت'];
			}
		}

		return $response;
	}


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

		$result =
		[
			[
				'method'       => 'editMessageReplyMarkup',
				// 'text'         => "بازگشت به منوی اصلی",
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


	/**
	 * create sarshomar menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function sarshomar($_onlyMenu = false)
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
		$txt_text .= "بزودی...";
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
	 * create psychology menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function psychology($_onlyMenu = false)
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
		$txt_text .= "بخش تست‌های روانشناسی به زودی راه‌اندازی خواهد شد.";
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
	 * create civility menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function civility($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				[""],
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
		$txt_text .= "بخش نظرسنجی‌های مردمی به زودی راه‌اندازی خواهد شد";
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
	 * show global message
	 * @return [type] [description]
	 */
	public static function global()
	{

		$result['caption'] = "_global_";
		// $result['photo']   = new \CURLFile(realpath("static/images/telegram/global.jpg"));
		$result['photo']   = 'AgADBAADuqcxG-eq1Ql_FJNHU8eJL6xEKRkABM3ZsAhFwe5jk3YBAAEC';
		$result['method']  = "sendPhoto";

		return $result;
	}


	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function list($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["مدرن", "استاندارد"],
				["مجلل", "خانواده"],
				["بازگشت به منوی معرفی"],
			],
		];

		if($_onlyMenu)
		{
			return $menu;
		}

		$result =
		[
			[
				'text'         => "_list_",
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}






	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_loc()
	{
		$result['text']        = 'منوی موقعیت'."\r\n";
		$result['replyMarkup'] =
		[
			'keyboard' =>
			[
				[
					[
						'text'            => 'تقاضای شماره تلفن',
						'request_contact' => true
					],
					[
						'text'             => 'تقاضای آدرس',
						'request_location' => true
					]
				]
			]
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_inline()
	{
		$result['text']        = 'منوی اینلاین آزمایشی'."\r\n";
		$result['replyMarkup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text'          => '<',
						'callback_data' => 'go_left'
					],
					[
						'text'          => '^',
						'callback_data' => 'go_up'
					],
					[
						'text'          => '>',
						'callback_data' => 'go_right'
					],
				],
				[
					[
						'text' => 'open google.com',
						'url'  => 'google.com'
					],
					[
						'text'                => 'search \'test\' inline',
						'switch_inline_query' => 'test'
					],
				]
			],
		];
		return $result;
	}
}
?>