<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\handle;

class step_create
{
	private static $menu = ["hide_keyboard" => true];
	private static $step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null)
	{
		step::start('create');
		return self::step1();
	}


	public static function step1()
	{
		// do not need to save text of contact if called!
		// step::set('saveText', false);
		// go to next step
		step::plus(1);
		// increase custom number
		step::plus(1, 'i');
		// create output text
		$txt_text = "نظرسنجی خود را تعریف نمایید\n";
		$txt_text .= "راهنمای ثبت نظرسنجی\n";
		$txt_text .= "۱. سوال نظرسنجی خود را در خط اول وارد نمایید.\n";
		$txt_text .= "۲. هر پاسخ را در یک خط مجزا وارد کنید.\n";
		$txt_text .= "می‌توانید از علائم زیر استفاده نمایید:\n";
		$txt_text .= ": برای تعریف نظرسنجی مرتب‌سازی استفاده می‌شود که در ابتدای سوال نظرسنجی می‌آید\n";
		$txt_text .= "+ اگر نظرسنجی شما دارای گزینه صحیح است ابتدای هر گزینه صحیح می‌باشد";
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => [
				"inline_keyboard" => [
					[
						[
							"text" => "انصراف",
							"callback_data" => 'create/cancel'
						],
						[
							"text" => "حذف راهنما",
							"callback_data" => 'create/helep/cancel'
						]
					]
				]
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
	public static function step2($_question)
	{
		// go to next step
		// step::plus();
		// set title for question
		step::set('textTitle', 'question');
		// increase custom number
		step::plus(1, 'i');
		// create output text
		$txt_text = "نظرسنجی شما ثبت شد \n";
		$txt_text .= "کد نظرسنجی شما\n/o\_3pf";

		// get name of question
		$result   =
		[
		'text'         => $txt_text
		];
		step::stop();
		// return menu
		return $result;
	}


	/**
	 * [step3 description]
	 * @param  [type] $_item [description]
	 * @return [type]        [description]
	 */
	public static function step3($_item)
	{
		// increase custom number
		step::plus(1, 'num');
		// create output text
		$txt_text = "مرحله ". step::get('i')."\n\n";
		$txt_text .= "گزینه ". self::$step_shape[step::get('num')] ." ثبت شد.\n*";
		$txt_text .= $_item;
		$txt_text .= "*\n\nلطفا گزینه بعدی را وارد نمایید.";
		$txt_text .= "\nدر صورت به اتمام رسیدن گزینه ها، کافی است عبارت /done را ارسال نمایید.";

		// get name of question
		$result   =
		[
		[
		'text'         => $txt_text,
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
	public static function stop($_cancel = false, $_text = null)
	{
		// set
		step::set('textTitle', 'stop');
		var_dump(step::get('text'));
		if($_cancel === true)
		{
			if($_text)
			{
				$final_text = $_text;
			}
			else
			{
				$final_text = "انصراف از ثبت نظرسنجی\n";
			}
		}
		elseif(\lib\db\polls::save(step::get('text'), bot::$user_id))
		{
			$final_text = "ثبت نظرسنجی با موفقیت به اتمام رسید.\n";
		}
		else
		{
			$final_text = "مشکلی در داده‌های ورودی یافت شد!\n";
			$final_text .= "لطفا دوباره تلاش کنید";
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

}
?>