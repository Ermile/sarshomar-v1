<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class step_define
{
	private static $menu = ["hide_keyboard" => true];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('define');

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
		// show give contact menu
		$menu     = menu_profile::getContact(true);
		$txt_text = "برای تعریف نظرسنجی جدید و ثبت آن، ما نیاز به ثبت‌نام در سیستم دارید.\n";
		$txt_text .= "بدین منظور کافی است از طریق منوی زیر اطلاعات مخاطب خود را برای ما ارسال نمایید تا ثبت نام شما در سیستم انجام شود.";

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



	public static function step2()
	{
		$cmd = bot::$cmd;
		// if user send his/her profile contact detail
		switch ($cmd['command'])
		{
			case 'type_phone_number':
				step::plus();
				// show step3 for define question
				$result   = self::step3();
				// define text of give contact
				$txt_text = "ثبت مخاطب شما با موفقیت به انجام رسید.\n";
				$txt_text .= "به راحتی نظرسنجی خود را ثبت کنید:)";
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
				// else send messge to attention to user to only send contact detail
				$txt_text = "لطفا تنها از طریق منوی زیر اقدام نمایید.\n";
				$txt_text .= "ما برای ثبت نظرسنجی به اطلاعات مخاطب شما نیاز داریم.";

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



	public static function step3()
	{
		step::plus(1, 'i');
		$txt_text = "مرحله ". step::get('i')."\n\n";
		$txt_text .= "برای تعریف نظرسنجی جدید در ابتدا سوال خود را وارد کنید.";
		$menu     = self::$menu;
		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		step::plus(1);

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
		step::plus();

		$_text = "سوال شما با موفقیت ثبت شد.\n*";
		$_text .= $_question;
		$_text .= "*\n\nلطفا گزینه اول را وارد نمایید.";

		// get name of question
		$result   =
		[
			[
				'text'         => $_text,
				'reply_markup' => self::$menu,
			],
		];
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
		step::plus('num');
		// step::plus();
		$_text = "گزینه ". step::get('num') ." ثبت شد.\n*";
		$_text .= $_item;
		$_text .= "*\n\nلطفا گزینه بعدی را وارد نمایید.";
		$_text .= "\nدر صورت به اتمام رسیدن گزینه ها، کافی است عبارت /done را ارسال نمایید.";

		// get name of question
		$result   =
		[
			[
				'text'         => $_text,
				'reply_markup' => self::$menu,
			],
		];
		// return menu
		return $result;
	}


	/**
	 * end define new question
	 * @return [type] [description]
	 */
	public static function stop($_cancel = false)
	{
		$_text = "ثبت نظرسنجی با موفقیت به اتمام رسید.\n";
		if($_cancel === true)
		{
			$_text = "انصراف از ثبت نظرسنجی\n";
		}
		step::stop();

		// get name of question
		$result   =
		[
			[
				'text'         => $_text,
				'reply_markup' => self::$menu,
			],
		];
		// return menu
		return $result;
	}
}
?>