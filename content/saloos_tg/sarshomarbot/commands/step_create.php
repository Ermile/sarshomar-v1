<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\markdown_filter;

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
		// step::plus(1, 'i');
		// create output text
		$txt_text = "سوال نظرسنجی را در خط اول وارد نمایید\n";
		$txt_text .= "پاسخ‌های خود را به ترتیب در هر خط قرار دهید\n";
		$txt_text .= "هر نظرسنجی باید حداقل دو پاسخ داشته باشد\n";
		// $txt_text .= "راهنمای ثبت نظرسنجی\n";
		// $txt_text .= "۱. سوال نظرسنجی خود را در خط اول وارد نمایید.\n";
		// $txt_text .= "۲. هر پاسخ را در یک خط مجزا وارد کنید.\n";
		// $txt_text .= "می‌توانید از علائم زیر استفاده نمایید:\n";
		// $txt_text .= ": برای تعریف نظرسنجی مرتب‌سازی استفاده می‌شود که در ابتدای سوال نظرسنجی می‌آید\n";
		// $txt_text .= "+ اگر نظرسنجی شما دارای گزینه صحیح است ابتدای هر گزینه صحیح می‌باشد";
		// $txt_text = 'hi';
		$result   =
		[
		'text'         => $txt_text ."\n#create",
		"response_callback" => utility::response_expire('create'),
		'reply_markup' => [
			"inline_keyboard" => [
				[
					[
						"text" => "انصراف",
						"callback_data" => 'create/cancel'
					]
				]
			]
		]
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

		$txt_text = "نظرسنجی شما ثبت شد \n";
		$txt_text .= "کد نظرسنجی شما\n";
		$question = markdown_filter::italic($_question);
		$question = markdown_filter::bold($question);
		$question = markdown_filter::link($question);
		$question = markdown_filter::remove_external_link($question);
		$question = markdown_filter::line_trim($question);
		$question_export = preg_split("[\n]", $question);
		if(count($question_export) < 2)
		{
			$txt_text = 'برای ثبت نظرسنجی باید حداقل سه خط وارد شود.';
			$txt_text .= "\n";
			$txt_text .= 'خط اول: سوال نظرسنجی.';
			$txt_text .= "\n";
			$txt_text .= 'خط دوم: جواب اول نظرسنجی.';
			$txt_text .= "\n";
			$txt_text .= 'خط سوم: جواب دوم نظرسنجی.';
			$txt_text .= "\n";
			$txt_text .= 'به ترتیب هر خطی نشانگر یک جواب می‌باشد';
			$markup = [
			"inline_keyboard" => [
					[
						[
							"text" => "انصراف",
							"callback_data" => 'create/cancel'
						]
					]
				]
			];
			$result   =[
			'text' => $txt_text,
			"reply_markup" => $markup,
			"response_callback" => utility::response_expire('create')
			];
		}
		else
		{
			$save = \lib\db\polls::insert_quick([
				'user_id' => bot::$user_id,
				'title'=> $question_export[0],
				'answers' => array_slice($question_export, 1)
				]);
			$txt_text .= $question;
			$txt_text = "باشه تو خووووووووب\n" . $txt_text;
			step::stop();
			$markup = null;
			$result   =['text' => $txt_text];
		}
		// get name of question
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