<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class steps_define
{
	private static $menu = ["hide_keyboard" => true];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		steps::start('define');
		// define
		$txt_text = "مرحله ۱\n\n";
		$txt_text .= "برای تعریف نظرسنجی جدید در ابتدا سوال خود را وارد کنید.";
		$menu     =
		[
			"hide_keyboard" => true,
			"force_reply"   => true
		];

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
	 * [step1 description]
	 * @param  [type] $_question [description]
	 * @return [type]            [description]
	 */
	public static function step1($_question)
	{
		steps::next();
		//
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
	 * [step2 description]
	 * @param  [type] $_item [description]
	 * @return [type]        [description]
	 */
	public static function step2($_item)
	{
		// steps::next();
		$_text = "گزینه ". steps::counter() ." ثبت شد.\n*";
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
	public static function stop()
	{
		steps::stop();
		$_text = "ثبت نظرسنجی با موفقیت به اتمام رسید.\n";

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