<?php
namespace content\saloos_tg\germile_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class step_register
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_caller, $_lastStep = 'start')
	{
		step::start('register');
		// set caller name and step
		step::set('call_from', $_caller);
		step::set('call_from_step', $_lastStep);

		if(bot::$user_id)
		{
			return self::finish('registered');
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
		// // do not need to save text of contact if called!
		// step::set('saveText', false);
		// show give contact menu
		$menu     = menu_profile::getContact(true);
		$txt_text = "کاربر گزامی، برای دسترسی به تمام عملکردهای این سرویس، ما نیاز به ثبت شماره تماس شما داریم.\n";
		$txt_text .= "بدین منظور کافی است از طریق منوی زیر اطلاعات مخاطب خود را برای ما ارسال نمایید تا ثبت نام شما انجام شود.";
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];
		// return menu
		return $result;
	}


	/**
	 * handle user input
	 * @return [type] [description]
	 */
	public static function step2()
	{
		// // do not need to save text of contact if called!
		// step::set('saveText', false);
		// increase limit valu
		step::plus(1, 'limit');
		// if user more than 3 times do not send contact go to main menu
		if(step::get('limit') >3)
		{
			// call stop function
			return self::finish('limit');
		}

		$cmd = bot::$cmd;
		// if user send his/her profile contact detail
		switch ($cmd['command'])
		{
			case 'type_contact':
				// show successful for define question
				$result = self::finish('successful');
				break;

			case 'بازگشت':
			case 'return':
			case '/return':
				$result = self::finish('return');
				break;

			default:
				$result = self::finish('wrong');
				break;
		}

		return $result;
	}


	/**
	 * finisht register process
	 * @param  [type] $_status [description]
	 * @return [type]          [description]
	 */
	private static function finish($_status)
	{
		$result =
		[
			'text'         => "",
			'reply_markup' => menu::main(true),
		];
		switch ($_status)
		{
			case 'limit':
				$txt = "دوست عزیز\n";
				$txt .= "ما برای سرویس دهی به شما نیاز به ثبت نام شما با شماره موبایل داریم.\n";
				$txt .= "در صورت عدم تمایل به ثبت شماره موبایل ما قادر به سرویس‌دهی به شما نیستیم.\n";
				$result['text'] = $txt;
				step::stop();
				break;

			case 'successful':
				$txt    = "ثبت مخاطب شما با موفقیت به انجام رسید.\n";
				// $txt    .= "به راحتی نظرسنجی خود را ثبت کنید:)";
				$result['text']         = $txt;
				$result['reply_markup'] = null;
				bot::sendResponse($result);
			case 'registered':
				// check if want to come back to specefic step, do it
				$result = self::successful();
				break;

			case 'failed':
				$result = false;
			case 'return':
				$txt            = "انصراف از ثبت مخاطب و بازگشت به منوی اصلی\n";
				$result['text'] = $txt;
				step::stop();
				break;

			case 'wrong':
			default:
				// else send messge to attention to user to only send contact detail
				$txt  = "لطفا تنها از طریق منوی زیر اقدام نمایید.\n";
				$txt  .= "ما برای ثبت نظرسنجی به اطلاعات مخاطب شما نیاز داریم.";
				$menu = menu_profile::getContact(true);
				$result['text']         = $txt;
				$result['reply_markup'] = $menu;
				break;
		}
		// return menu
		return $result;
	}


	/**
	 * on successful ending call caller step
	 * @return [type] [description]
	 */
	private static function successful()
	{
		// generate caller function to continue last step
		$funcName = step::get('call_from'). "::". step::get('call_from_step');
		// stop registration after use variables
		step::stop();
		if(is_callable($funcName))
		{
			// get and return response
			$result = call_user_func($funcName, null, true);
		}
		// else show main menu
		else
		{
			$txt = "بازگشت به منوی اصلی\n";
			$result =
			[
				'text'         => $txt,
				'reply_markup' => menu::main(true),
			];
			// show main menu
		}
		return $result;
	}

}
?>