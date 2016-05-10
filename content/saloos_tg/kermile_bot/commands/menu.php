<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu
{
	public static $return = false;

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
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی اصلی':
					default:
						$response = user::start();
						break;
					case 'بازگشت به ثبت سفارش':
						$response = self::order();
						break;
				}
				// $response = self::returnBtn();
				break;

			case 'مشاهده':
				$response = self::showMenu();
				break;

			case 'نوشیدنی':
				$response = self::drink();
				break;

			case 'مخلفات':
				$response = self::other();
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
				["بازگشت به منوی اصلی"]
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
	 * showMenu
	 * @return [type] [description]
	 */
	public static function showMenu()
	{
		// $menu =
		// [
		// 	'keyboard' =>
		// 	[
		// 		["ساندویچ", "پیتزا"],
		// 		["مخلفات", "نوشیدنی"],
		// 	],
		// ];

		$txt_caption = "محصولات فست فود کرمایل.\nشما می توانید منوی ما را در گوشی یا رایانه خود ذخیره کنید.";
		$result =
		[
			[
				'caption'   => $txt_caption,
				'method' => 'sendPhoto',
				// 'photo'  => new \CURLFile(realpath("static/images/telegram/kermile/menu.jpg")),
				'photo'  => 'AgADBAADracxGxBxeQyJeNqkhwcFJxP1KBkABEaZHHvrygd_hOcBAAEC',
			],
		];
		// $result['reply_markup'] = $menu;

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
					["بازگشت به ثبت سفارش"]
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
		$result['text'] = "شما پیتزا پپرونی را انتخاب کرده اید.\n";
		$result['text'] .= "لطفا از منوی زیر تعداد را انتخاب نمایید یا درصورت تمایل به سفارش تعداد بیشتر مقدار آن را با کیبورد وارد نمایید.";
		$result['reply_markup'] =
		[
			// 'keyboard' =>
			// [
			// 	["1", "2", "3", "4", "5"],
			// 	["6", "7", "8", "9", "10"],
			// 	["انصراف"],
			// ],
			// "one_time_keyboard" => true,
			"force_reply_keyboard" => true,
			'selective' => true,
		];
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
	 * drink
	 * @return [type] [description]
	 */
	public static function drink()
	{
		$result['text'] = "لطفا نوشیدنی مورد نظر خود را انتخاب کنید";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["آب", "نوشابه"],
					["دلستر", "آبمیوه"],
			],
		];
		return $result;
	}

	/**
	 * other
	 * @return [type] [description]
	 */
	public static function other()
	{
		$result['text'] = "لطفا مخلفات مورد نظر خود را انتخاب کنید";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
					["سالاد فصل", "سالاد اندونزی"],
					["قارچ سوخاری", "سیب زمینی"],
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