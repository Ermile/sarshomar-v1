<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu
{
	public static $return = true;

	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case 'ثبت':
				$response = self::order();
				break;

			case 'ساندویچ':
				$response = self::sandwich();
				break;

			case 'همبرگر':
				$response = self::sandwich_hamburger();
				break;

			case 'چیزبرگر':
				$response = self::sandwich_cheeseburger();
				break;

			case 'هات':
				$response = self::sandwich_hotdog();
				break;

			case 'چیپس':
				$response = self::sandwich_chips();
				break;

			case 'پیتزا':
				$response = self::pizza();
				break;

			case 'پپرونی':
				$response = self::pizza_01();
				break;

			case 'menu':
			case 'منو':
				$response = self::menu0();
				break;

			case 'loc':
			case 'موقعیت':
				$response = self::menu_loc();
				break;


			case 'inline':
			case 'اینلاین':
				$response = self::menu_inline();
				break;

			case 'main':
			case 'mainmenu':
			case 'menu0':
			case 'منو۰':
				$response = self::menu_main();
				break;

			case 'return':
			case 'بازگشت':
				$response = self::returnBtn();
				break;

			default:
				break;
		}

		// automatically add return to end of keyboard
		if(self::$return)
		{
			// if has keyboard
			if(isset($response['reply_markup']['keyboard']))
			{
				$response['reply_markup']['keyboard'][] = ['بازگشت'];
			}
		}

		return $response;
	}


	/**
	 * order
	 * @return [type] [description]
	 */
	public static function order()
	{
		$menu =
		[
			'keyboard' =>
			[
				["ساندویچ", "پیتزا"],
				["مخلفات", "نوشیدنی"],
			],
		];

		$result['text'] = "لطفا یکی از دسته بندی ها را انتخاب کنید";
		$result['reply_markup'] = $menu;

		// $result   =
		// [
		// 	[
		// 		'text'         => "لطفا یکی از دسته بندی ها را انتخاب کنید",
		// 		'reply_markup' => $menu,
		// 	],
		// ];


		return $result;
	}

	/**
	 * sandwich
	 * @return [type] [description]
	 */
	public static function sandwich()
	{
		$result['text'] = "چه ساندویچی دوست دارید؟";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["چیزبرگر", "همبرگر"],
					["چیپس و پنیر", "هات داگ"],
			],
		];
		return $result;
	}

	/**
	 * sandwich_hamburger
	 * @return [type] [description]
	 */
	public static function sandwich_hamburger()
	{
		$result['text'] = "لطفا تعداد همبرگرها را وارد کنید";
		return $result;
	}

	/**
	 * sandwich_cheeseburger
	 * @return [type] [description]
	 */
	public static function sandwich_cheeseburger()
	{
		$result['text'] = "لطفا تعداد چیزبرگرها را وارد کنید";
		return $result;
	}

	/**
	 * sandwich_hotdog
	 * @return [type] [description]
	 */
	public static function sandwich_hotdog()
	{
		$result['text'] = "لطفا تعداد هات داگ ها را وارد کنید";
		return $result;
	}

	/**
	 * sandwich_chips
	 * @return [type] [description]
	 */
	public static function sandwich_chips()
	{
		$result['text'] = "لطفا تعداد چیپس و پنیر ها را وارد کنید";
		return $result;
	}

	/**
	 * sandwich_chips
	 * @return [type] [description]
	 */
	public static function pizza()
	{
		$result['text'] = "چه پیتزایی دوست دارید؟";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["یونانی", "پپرونی"],
					["سرآشپز", "سبزیجات"],
			],
		];
		return $result;
	}

	/**
	 * pizza_01
	 * @return [type] [description]
	 */
	public static function pizza_01()
	{
		$result['text'] = "";
		return $result;
	}




	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function returnBtn()
	{
		$result['text'] = 'بازگشت به منوی قبلی'."\r\n";
		$result['text'] .= 'مثلا برگشتیم'."\r\n";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["خوب"],
					["بد"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu0()
	{
		$result['text'] = 'منوی آزمایشی'."\r\n";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["سلام"],
					["خوبی"],
					["خوب", "بد"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_main()
	{
		// disable return from main menu
		self::$return          = false;
		$result['text']        = 'منوی اصلی'."\r\n";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["منوی آزمایش"],
					["نظرسنجی های من"],
					["مقالات"],
					["پروفایل"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_loc()
	{
		$result['text']        = 'منوی موقعیت'."\r\n";
		$result['reply_markup'] =
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
		$result['reply_markup'] =
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