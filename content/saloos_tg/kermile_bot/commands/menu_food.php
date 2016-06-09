<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu_food
{
	public static $return = false;

	/**
	 * order
	 * @return [type] [description]
	 */
	public static function main()
	{
		$menu =
		[
			'keyboard' =>
			[
				["ساندویچ", "پیتزا"],
				["مخلفات", "نوشیدنی"],
				// ["بازگشت به منوی اصلی"]
			],
		];

		$result['text']         = "لطفا یکی از دسته‌بندی‌ها زیر را انتخاب کنید\n\n";
		$result['text']         .= "/cancel انصراف از ثبت سفارش ";
		$result['reply_markup'] = $menu;

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
}
?>