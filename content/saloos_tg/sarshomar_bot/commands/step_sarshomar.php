<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class step_sarshomar
{
	private static $menu = ["hide_keyboard" => true];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('sarshomar');

		if(bot::$user_id)
		{
			step::goto(3);
			return self::step3();
		}
		else
		{
			return self::step1();
		}
	}


	/**
	 * show please send contact message
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// after this go to next step
		step::plus();
		// do not save input text in this step
		step::set('saveText', false);
		// show give contact menu
		$menu     = menu_profile::getContact(true);
		$txt_text .= "به دلیل نیاز برای منحصر بفرد بودن هر شخص و کار با نسخه وب‌سایت، ما نیاز به اطلاعات مخاطب شما داریم.\n\n";
		$txt_text .= "بدین منظور کافی است از طریق منوی زیر اطلاعات مخاطب خود را برای ما ارسال نمایید تا ثبت نام شما انجام شود.\n\n";

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
	 * wait to get contact detail
	 * @return [type] [description]
	 */
	public static function step2()
	{
		// do not save input text in this step
		// increase limit valu
		step::plus(1, 'limit');
		// if user more than 3 times do not send contact go to main menu
		if(step::get('limit') >3)
		{
			$txt_failedContact = "دوست عزیز\n";
			$txt_failedContact .= "ما برای سرویس دهی به شما نیاز به ثبت نام شما با شماره موبایل داریم.\n";
			$txt_failedContact .= "در صورت عدم تمایل به ثبت شماره موبایل ما قادر به سرویس‌دهی به شما نیستیم.\n";
			// call stop function
			step::stop();
			return self::stop(true, $txt_failedContact);
		}

		$cmd = bot::$cmd;
		// if user send his/her profile contact detail
		switch ($cmd['command'])
		{
			case 'type_phone_number':
				// go to next step
				step::plus();
				// show step3 for define question
				$result   = self::step3();
				// define text of give contact
				$txt_text = "ثبت مخاطب شما با موفقیت به انجام رسید.\n";
				$txt_text .= "حال می‌توانید از سرشمار به راحتی اسفتاده نمایید:)";
				// create contact msg
				$result_contact =
				[
					'text'         => $txt_text,
					'reply_markup' => self::$menu,
				];
				// first show contact given msg then questions
				array_unshift($result, $result_contact);
				break;

			case 'بازگشت':
				return step::stop(true);
				break;


			default:
				step::set('saveText', false);

				// else send messge to attention to user to only send contact detail
				$txt_text = "لطفا تنها از طریق منوی زیر اقدام نمایید.\n";
				$txt_text .= "ما برای ثبت‌نام، به اطلاعات مخاطب شما نیاز داریم.";

				$menu     = menu_profile::getContact(true);
				$result   =
				[
					[
						'text'         => $txt_text,
						'reply_markup' => $menu,
					],
				];
				break;
		}

		return $result;
	}


	/**
	 * get list of questions and ask a question
	 * @return [type] [description]
	 */
	public static function step3()
	{
		// go to next step
		step::plus();
		// set title for
		step::set('textTitle', 'question');
		// increase custom number
		step::plus(1, 'i');
		// create output text
		$txt_text = "سوال ". step::get('i')."\n\n";
		$txt_text .= "-- متن سوال --";

		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => self::answersKeyboard(),
			],
		];

		// return menu
		return $result;
	}



	/**
	 * [step2 description]
	 * @param  [type] $_question [description]
	 * @return [type]            [description]
	 */
	public static function step4($_question)
	{
		var_dump($_question);
		var_dump(self::answersKeyboard(true));
		if(in_array($_question, self::answersKeyboard(true)))
		{
			// go to next step
			step::plus();

			// increase custom number
			// create output text
			$txt_text = "پاسخ *سوال ". step::get('i')."*دریافت شد.\n\n";
			$menu =
			[
				'keyboard' =>
				[
					["سوال بعدی"],
					["مشاهده نتایج"],
					["بازگشت به منوی اصلی"],
				],
			];
			// get name of question
			$result   =
			[
				[
					'text'         => $txt_text,
					'reply_markup' => $menu,
				],
			];
		}
		else
		{
			var_dump('expression');
			$txt_text = 'لطفا یکی از گزینه‌های موجود را انتخاب نمایید!';
			$result   =
			[
				[
					'text'         => $txt_text,
					'reply_markup' => self::answersKeyboard(),
				],
			];
		}


		// return menu
		return $result;
	}


	/**
	 * [step3 description]
	 * @param  [type] $_item [description]
	 * @return [type]        [description]
	 */
	public static function step5($_item)
	{
		// create output text
		$txt_text = "سوال ". step::get('i')."\n\n";
		switch ($_item)
		{
			case 'سوال بعدی':
				step::goto(3);
				return self::step3();
				break;

			case 'مشاهده نتایج':
				$txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'بازگشت به منوی اصلی':
				step::stop(3);
				return self::stop();
				break;

			default:
				$txt_text = 'لطفا یکی از گزینه‌های زیر را انتخاب نمایید';
				break;
		}

		var_dump($_item);
		var_dump(bot::$cmd);

		// get name of question
		$result   =
		[
			[
				'text'         => $txt_text,
			],
		];
		// return menu
		return $result;
	}


	/**
	 * end define new question
	 * @return [type] [description]
	 */
	public static function stop($_cancel = false, $_text = null)
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
		}
		elseif(false)
		{
			$final_text = "ثبت نظرسنجی با موفقیت به اتمام رسید.\n";
		}
		else
		{
			$final_text = "مشکلی در داده‌های ورودی یافت شد!\n";
			$final_text .= "لطفا دوباره تلاش کنید";
			$final_text = "ثبت نظرسنجی با موفقیت به اتمام رسید. ۲\n";
		}

		// get name of question
		$result   =
		[
			[
				'text'         => $final_text,
				'reply_markup' => menu_my::my(true),
			],
		];
		// return menu
		return $result;
	}

	public static function answersKeyboard($_onlyArray = false)
	{
		$answersList = ['بله', 'خیر'];
		if($_onlyArray)
		{
			return $answersList;
		}

		$menu =
		[
			'keyboard' => [],
			"one_time_keyboard" => true,
		];
		foreach ($answersList as $key => $value)
		{
			$menu['keyboard'][] = [$value];
		}
		$menu['keyboard'][] = ['گزینه سوم'];

		return $menu;
	}
}
?>