<?php
namespace content\saloos_tg\germile_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class step_order
{
	private static $menu            = ["hide_keyboard" => true];
	private static $keyboard_number =
	[
		'keyboard' =>
		[
			['1', '2'],
			['3', '4', '5', '6'],
			['7', '8', '9', '10', '0'],
		],
	];
	private static $keyborad_final =
	[
		'keyboard' =>
		[
			// ["ادامه خرید", "مشاهده سبد خرید"],
			["ادامه خرید"],
			["اتمام سفارش"],
			["انصراف"],
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
			'reply_markup' => self::drawKeyboard('catList'),
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
		// get list of product in this category
		$productList = product::get($_txtCategory, true);
		// if category name is not exist or other problem show message
		if(!$productList || !is_array($productList))
		{
			$txt_text = 'لطفا یکی از گزینه‌های موجود را انتخاب نمایید!';
			$result =
			[
				'text'         => $txt_text,
				'reply_markup' => self::drawKeyboard('catList'),
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

				case 'پیش‌غذا':
					$txt_text = "لطفا پیش‌غذای مورد علاقه خود را انتخاب کنید";
					break;
			}
			$txt_text .= "\n\n";

			// set product list with price and desc
			foreach ($productList as $key => $productDetail)
			{
				$name  = $productDetail['name'];
				$price = $productDetail['price'];
				$desc  = $productDetail['desc'];
				$txt_text .= "$name `$price تومان`\n";
				if($desc)
				{
					// $txt_text .= "$desc \n\n";
				}
			}

			$productList = array_column($productList, 'name');

			// send photo of this category
			$result   = product::sendPhoto($_txtCategory);
			$result[] =
			[
				'text'         => $txt_text,
				// 'reply_markup' => null,
				'reply_markup' => self::drawKeyboard($productList),
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
		// check product exist or not
		if(!product::detail($_txtProduct, true))
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

			$txt_text = "لطفا تعداد $_txtProduct مورد نیاز را انتخاب کنید.";
			$txt_text .= "\nدر صورتی که تعداد مورد نیاز بیش از لیست است، مقدار آن را با کیبورد وارد کنید";
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
			$txt_text = 'لطفا تنها تعداد مورد نیاز خود را به صورت عددی وارد کنید!';
			$result   =
			[
				'text'         => $txt_text,
				'reply_markup' => self::$keyboard_number,
			];
		}
		elseif($_txtNumber > 100)
		{
			// product not exist
			$txt_text = 'این تعداد ساپورت نمی‌شود‍!';
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

			$txt_text = "*$_txtNumber عدد $product *به سبد خرید اضافه شد.\n";
			if($_txtNumber == 0)
			{
				$txt_text = "*$product *از سبد خرید حذف شد.\n";
			}

			$txt_text .= self::showCard();
			$result   =
			[
				'text'         => $txt_text,
				// 'reply_markup' => null,
				'reply_markup' => self::$keyborad_final
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
				step::goingto(1);
				return self::step1();
				break;

			case 'مشاهده سبد خرید':
			case 'مشاهده سفارش':
			case '/card':
			case 'card':
			case 'showcard':
				$txt_text = self::showCard();
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'اتمام سفارش':
			case '/paycart':
			case 'paycart':
				$txt_text = self::showCard();
				step::plus();
				return self::step6();
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'بازگشت به منوی اصلی':
			case 'انصراف':
			case '/cancel':
			case 'cancel':
			case '/stop':
			case 'stop':
			case '/return':
			case 'return':
				return self::stop();
				break;

			default:
				$txt_text = 'لطفا یکی از گزینه‌های زیر را انتخاب نمایید';
				break;
		}


		// get name of question
		$result   =
		[
			'text' => $txt_text,
		];
		// return menu
		return $result;
	}


	public static function step6()
	{
		$final_text = "سفارش شما تکمیل شد.\n";
		$final_text .= "تا دقایقی دیگر سفارش شما ارسال خواهد شد.\n";

		$result   =
		[
			'text'         => $final_text,
			'reply_markup' => menu::main(true),
			// 'reply_markup' => null,
			// 'reply_markup' => $menu
		];
		// send order to admin of bot
		self::sendOrder();
		// stop order
		step::stop();
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
				$final_text = "انصراف از ثبت سفارش\n";
			}
			step::stop();
		}
		elseif($_cancel === false)
		{
			$final_text = "سفارش شما تکمیل شد.\n";
			$final_text .= "تا دقایقی دیگر سفارش شما ارسال خواهد شد.\n";
			// complete soon
			step::stop();
		}
		else
		{
			$final_text = "انصراف\n";
			step::stop(true);
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
	public static function drawKeyboard($_list = null, $_onlyArray = null)
	{
		if(is_string($_list))
		{
			$_list = product::get($_list);
		}

		if($_onlyArray === true)
		{
			// return array contain only list
			$_list = array_keys($_list);
			return $_list;
		}

		$menu =
		[
			'keyboard' => [],
			"one_time_keyboard" => true,
		];

		// calculate number of item in each row
		// max row can used is 3
		$inEachRow  = 1;
		$itemsCount = count($_list);
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
		foreach ($_list as $key => $value)
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
		if($_quantity == 0 && isset($myorder[$_category][$_product]))
		{
			unset($myorder[$_category][$_product]);
			if(isset($myorder[$_category]) && count($myorder[$_category]) === 0)
			{
				unset($myorder[$_category]);
			}
		}
		// save new order
		step::set('order', $myorder);
	}


	/**
	 * show user card
	 * @return [type] [description]
	 */
	private static function showCard()
	{
		$myorder    = step::get('order');
		$txt_card   = "سبد خرید\n";
		$totalPrice = 0;
		if(count($myorder) === 0 )
		{
			$txt_card   = "سبد خرید خالی است!\n";
		}
		else
		{
			foreach ($myorder as $category => $productList)
			{
				$txt_card .= "`$category`\n";
				foreach ($productList as $product => $quantity)
				{
					$productDetail = product::detail($product);
					$price = $productDetail['price'];
					$totalPrice += $quantity * $price;
					$txt_card .= "▫️ $product *". $quantity. "* ✕ `". $price. "`\n";
				}
			}
			$txt_card .= "\nجمع کل:* $totalPrice تومان* 💰";
		}
		return $txt_card;
	}


	private static function saveCard()
	{
			\lib\db\posts::insertOrder(bot::$user_id, $question_id, $answer_id, $_answer_txt);

	}


	// send order to admin
	private static function sendOrder($_desc = null)
	{
		$text   = "🚩 📨 سفارش جدید از ";
		$text   .= bot::response('from', 'first_name');
		$text   .= ' '. bot::response('from', 'last_name');
		$text   .= ' @'. bot::response('from', 'username');
		$text   .= "\n$_desc\n";
		$text   .= self::showCard();
		$text   .= "\nکد کاربر ". bot::response('from');

		$menu =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text'          => 'ثبت در سیستم',
						'callback_data' => 'order_register',
					],
				],
				[
					[
						'text'          => 'کاربر نیاز به تایید',
						'callback_data' => 'order_verification',
					],
				],
			],
		];

		$result =
		[
			'method'       => 'sendMessage',
			'text'         => $text,
			'chat_id'      => '46898544',
			'reply_markup' => $menu,

		];
		var_dump($result);
		$result = bot::sendResponse($result);
		var_dump($result);
		return $result;
	}
}
?>