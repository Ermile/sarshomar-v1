<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class step_order
{
	private static $menu      = ["hide_keyboard" => true];
	private static $menuItems =
	[
		"ساندویچ"  => ["چیزبرگر", "همبرگر", "چیپس و پنیر", "هات‌داگ"],
		"پیتزا"   => ["یونانی", "پپرونی", "سرآشپز", "سبزیجات"],
		"مخلفات"  => ["سالاد فصل", "سالاد اندونزی", "قارچ سوخاری", "سیب زمینی"],
		"نوشیدنی" => ["آب", "نوشابه", "دلستر", "آبمیوه"],
	];

	private static $keyboard_number =
	[
		'keyboard' =>
		[
			["1", "2", "3", "4"],
			["5", "6", "7", "8"],
			["9", "10", "11", "12"],
		],
	];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_skip = null)
	{
		$result = null;
		if($_skip !== true)
		{
			$result = step_register::start(__CLASS__, __FUNCTION__);
		}
		// if we have result or want to skip, then call step1
		if($result === true || $_skip === true)
		{
			step::start('order');
			return self::step1();
		}
		else
		{
			// do nothing, wait for registration
			return $result;
		}
	}


	/**
	 * get list of food type and show it to user for select
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// go to next step, step4
		step::plus();
		// set title for
		step::set('textTitle', 'foodType');
		// increase custom number
		step::plus(1, 'i');
		// create output message
		$txt_text = "لطفا یکی از دسته‌بندی‌های زیر را انتخاب کنید\n\n";
		$txt_text .= "/cancel انصراف از ثبت سفارش ";
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => self::drawKeyboard(),
		];

		return $result;
	}



	/**
	 * select food product name
	 * @param  [type] $_answer_txt [description]
	 * @return [type]            [description]
	 */
	public static function step2($_txtCategory)
	{
		// get answer id from answers list
		$productList = self::drawKeyboard($_txtCategory);
		// if category name is not exist or other problem show message
		if(!$productList || !is_array($productList))
		{
			$txt_text = 'لطفا یکی از گزینه‌های موجود را انتخاب نمایید!';
			$result   =
			[
				'text'         => $txt_text,
				'reply_markup' => self::drawKeyboard(),
			];
		}
		else
		{
			// go to next step
			step::plus();
			// save category
			step::set('order_category', $_txtCategory);
			// get question id

			$txt_text = "لطفا کالای مورد نظر را انتخاب کنید";
			switch ($_txtCategory)
			{
				case 'پیتزا':
					$txt_text = "چه نوع پیتزایی دوست دارید؟";
					break;

				case 'ساندویچ':
					$txt_text = "چه ساندویجی نیاز دارید؟";
					break;

				case 'نوشیدنی':
					$txt_text = "لطفا نوع نوشیدنی را انتخاب کنید";
					break;

				case 'مخلفات':
					$txt_text = "لطفا نوع کالا را انتخاب کنید";
					break;
			}

			$result   =
			[
				'text'         => $txt_text,
				// 'reply_markup' => null,
				'reply_markup' => self::drawKeyboard($_txtCategory),
			];
		}

		// return menu
		return $result;
	}


	/**
	 * select food count needed
	 * @param  [type] $_answer_txt [description]
	 * @return [type]            [description]
	 */
	public static function step3($_txtProduct)
	{
		$category    = step::get('order_category');
		$productList = self::drawKeyboard($category, true);

		if(!in_array($_txtProduct, $productList))
		{
			// product not exist
			$txt_text = "لطفا یکی از کالاهای موجود در دسته $category را انتخاب نمایید!";
			$result   =
			[
				'text'         => $txt_text,
				'reply_markup' => self::drawKeyboard($category),
			];
		}
		else
		{
			// product exist, go to next step
			// go to next step
			step::plus();
			// save product name
			step::set('order_product', $_txtProduct);

			$txt_text = "لطفا تعداد $_txtProduct مورد نیاز را انتخاب کنید";
			// $txt_text = "لطفا از منوی زیر تعداد را انتخاب نمایید یا درصورت تمایل به سفارش تعداد بیشتر مقدار آن را با کیبورد وارد نمایید.";

			$result   =
			[
				'text'         => $txt_text,
				// 'reply_markup' => null,
				'reply_markup' => self::$keyboard_number,
			];

		}

		// return menu
		return $result;
	}


	/**
	 * show continue menu
	 * @param  [type] $_answer_txt [description]
	 * @return [type]            [description]
	 */
	public static function step4($_txtNumber)
	{
		$category = step::get('order_category');
		$product  = step::get('order_product');

		// if user pass anything except number show menu again
		if(!is_numeric($_txtNumber))
		{
			// product not exist
			$txt_text = 'لطفا تعداد مورد نیاز خود را وارد کنید!';
			$result   =
			[
				'text'         => $txt_text,
				'reply_markup' => self::$keyboard_number,
			];
		}
		else
		{
			// product exist, go to next step
			// go to next step
			step::plus();
			// save product quantity
			step::set('order_quantity', $_txtNumber);
			// add to catd
			self::addToCard($category, $product, $_txtNumber);


			$txt_text = "تعداد* $_txtNumber عدد $product *به سبد خرید شما اضافه شد\n";
			$menu     =
			[
				'keyboard' =>
				[
					["ادامه خرید"],
					["مشاهده سبد خرید"],
					["اتمام سفارش"],
					["بازگشت به منوی اصلی"],
				],
			];
			$result   =
			[
				'text'         => $txt_text,
				// 'reply_markup' => null,
				'reply_markup' => $menu
			];

		}

		// return menu
		return $result;
	}


	/**
	 * show last menu
	 * @param  [type] $_item [description]
	 * @return [type]        [description]
	 */
	public static function step5($_item)
	{
		// create output text
		$txt_text = "سوال ". step::get('i')."\n\n";
		switch ($_item)
		{
			case 'ادامه خرید':
			case '/next':
			case 'next':
				step::goto(1);
				return self::step1();
				break;

			case 'مشاهده سبد خرید':
			case '/card':
			case 'card':
			case 'showcard':
				var_dump(step::get('order'));
				$txt_text = self::showCard();
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'اتمام سفارش':
			case '/paycart':
			case 'paycart':
				$txt_text = self::showCard();
				step::plus();
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'بازگشت به منوی اصلی':
			case '/cancel':
			case 'cancel':
			case '/stop':
			case 'stop':
			case '/return':
			case 'return':
				step::stop(3);
				return self::stop();
				break;

			default:
				$txt_text = 'لطفا یکی از گزینه‌های زیر را انتخاب نمایید';
				break;
		}


		// get name of question
		$result   =
		[
			[
				'text' => $txt_text,
			],
		];
		// return menu
		return $result;
	}


	public static function step6($_item)
	{
		$result   =
		[
			'text'         => "هدایت برای پرداخت...",
			// 'reply_markup' => null,
			// 'reply_markup' => $menu
		];
		return $result;
	}


	/**
	 * end define new question
	 * @return [type] [description]
	 */
	public static function stop($_cancel = null, $_text = null)
	{
		// set
		step::set('textTitle', 'stop');


		if($_cancel === true)
		{
			if($_text)
			{
				$final_text = $_text;
			}
			else
			{
				$final_text = "انصراف از ادامه پاسخ‌دهی به نظرسنجی‌ها\n";
			}
			step::stop();
		}
		elseif($_cancel === false)
		{
			$final_text = "شما به همه سوالات پاسخ دادید!\n";
			$final_text .= "آیا مایلید پس از اضافه شدن نظرسنجی‌های جدید به شما اطلاع دهیم؟\n";
			// complete soon
			step::stop();
		}
		else
		{
			$final_text = "ممنون از اینکه زمان ارزشمند خود را در اختیار ما قرار دادید.\n";
		}

		// get name of question
		$result   =
		[
			[
				'text'         => $final_text,
				'reply_markup' => menu::main(true),
			],
		];
		// return menu
		return $result;
	}


	/**
	 * return answer keyborad array or keyboard
	 * @param  boolean $_onlyArray [description]
	 * @return [type]              [description]
	 */
	public static function drawKeyboard($parent = null, $_onlyArray = null)
	{
		$answersList = self::$menuItems;
		if(!$parent)
		{
			$answersList = array_keys($answersList);
		}
		elseif($parent && isset($answersList[$parent]))
		{
			$answersList = $answersList[$parent];
		}
		else
		{
			return false;
		}

		if($_onlyArray === true)
		{
			return $answersList;
		}
		elseif($_onlyArray === false)
		{
			$txt_answers = "\n";
			foreach ($answersList as $key => $value)
			{
				$txt_answers .= '/'. $key. '. '. html_entity_decode($value)."\n";
			}
			return $txt_answers;
		}
		$menu =
		[
			'keyboard' => [],
			"one_time_keyboard" => true,
		];

		// calculate number of item in each row
		// max row can used is 3
		$inEachRow  = 1;
		$itemsCount = count($answersList);
		$rowUsed    = $itemsCount;
		$rowMax     = 4;
		// if count of items is divided by 2
		if(($itemsCount % 2) === 0)
		{
			$inEachRow = 2;
			$rowUsed   = $itemsCount / 2;
			if($rowUsed > $rowMax)
			{
				if(($itemsCount % 3) === 0)
				{
					$inEachRow = 3;
					$rowUsed   = $itemsCount / 3;
				}
			}
		}
		// if count of items is divided by 3
		if($itemsCount > 6 && ($itemsCount % 3) === 0)
		{
			$inEachRow = 3;
			$rowUsed   = $itemsCount / 3;
		}

		$i = 0;
		foreach ($answersList as $key => $value)
		{
			// calc row number
			$row = floor($i/ $inEachRow);
			// add to specefic row
			$menu['keyboard'][$row][] = $value;
			// increment counter
			$i++;
		}
		// $menu['keyboard'][] = ['گزینه سوم'];
		return $menu;
	}


	/**
	 * save new product to card
	 * @param [type] $_category [description]
	 * @param [type] $_product  [description]
	 * @param [type] $_quantity [description]
	 */
	private static function addToCard($_category, $_product, $_quantity)
	{
		// get current order
		$myorder = step::get('order');
		// add this product to order
		$myorder[$_category][$_product] = $_quantity;
		// save new order
		step::set('order', $myorder);
	}



	private static function showCard()
	{
		$myorder  = step::get('order');
		$txt_card = "📃 سبد خرید شما\n\n";
		foreach ($myorder as $category => $productList)
		{

			foreach ($productList as $product => $quantity)
			{
				$txt_card .= " 🔖". $category ." - ". $product ." ". $quantity. "عدد\n";
			}
		}
		$txt_card .= "\n\n /cancel انصراف از خرید";
		return $txt_card;
	}
}
?>