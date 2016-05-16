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
		// do not save input text in this step
		step::set('saveText', false);
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
				// step::plus();
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
				step::set('saveText', false);

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
		// go to next step
		step::plus(1);
		// set title for
		step::set('textTitle', 'question');
		// increase custom number
		step::plus(1, 'i');
		// create output text
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
		// go to next step
		step::plus();

		// increase custom number
		step::plus(1, 'i');
		// create output text
		$txt_text = "مرحله ". step::get('i')."\n\n";
		$txt_text .= "سوال شما با موفقیت ثبت شد.\n*";
		$txt_text .= $_question;
		$txt_text .= "*\n\nلطفا گزینه اول را وارد نمایید.";

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
	 * [step3 description]
	 * @param  [type] $_item [description]
	 * @return [type]        [description]
	 */
	public static function step5($_item)
	{
		// increase custom number
		step::plus(1, 'num');
		// create output text
		$txt_text = "مرحله ". step::get('i')."\n\n";
		$txt_text .= "گزینه ". step::get('num') ." ثبت شد.\n*";
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
		elseif(self::savePoll())
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


	/**
	 * save poll into database
	 * @return [type] [description]
	 */
	private static function savePoll()
	{
		$userInput = step::get('text');
		var_dump($userInput);
		// return false if count of input value less than 3
		// 1 question
		// 2 answer or more
		if(count($userInput) < 3 || count($userInput) > 10 || !isset($userInput['question']))
		{
			return false;
		}
		// extract question
		$question = $userInput['question'];
		unset($userInput['question']);
		// save question into post table
		$saveQusStatus = self::saveQuestion($question, $userInput);
		// save answers into options table
		$saveAnsStatus = self::saveAns($userInput, $saveQusStatus);
		// return final result
		return $saveAnsStatus;
	}


	/**
	 * save question into post table
	 * @param  [type] $_question    [description]
	 * @param  [type] $_answersList [description]
	 * @return [type]               [description]
	 */
	public static function saveQuestion($_question, $_answersList)
	{
		$slug         = \lib\utility\filter::slug($_question);
		$user         = bot::$user_id;
		$url          = 'civility/'.$user.'/'.$slug;
		$_answersList = json_encode($_answersList, JSON_UNESCAPED_UNICODE);
		$pubDate      = date('Y-m-d H:i:s');
		// create query string
		$qry = "INSERT INTO posts
		(
			`post_language`,
			`post_title`,
			`post_slug`,
			`post_url`,
			`post_meta`,
			`post_type`,
			`post_status`,
			`post_publishdate`,
			`user_id`
		)
		VALUES
		(
			'fa',
			'$_question',
			'$slug',
			'$url',
			'$_answersList',
			'poll',
			'draft',
			'$pubDate',
			$user
		)";
		// run query
		$result  = \lib\db::query($qry);
		// return last insert id
		return \lib\db::insert_id();
	}


	/**
	 * save answers into options table
	 * @param  [type] $_answersList raw answer list
	 * @return [type]               [description]
	 */
	public static function saveAns($_answersList, $_post_id)
	{
		$answers = [];
		$max_ans = 10;
		// foreach answers exist fill the array
		foreach ($_answersList as $key => $value)
		{
			$answers[$key]['txt'] = $value;
		}
		// decode for saving into db
		$answers     = json_encode($answers, JSON_UNESCAPED_UNICODE);
		$option_data =
		[
			'post'   => $_post_id,
			'cat'    => 'meta_polls',
			'key'    => 'answers_'.$_post_id,
			'value'  => "",
			'meta'   => $answers,
			'status' => 'enable',
		];
		// save in options table and if successful return session_id
		return \lib\utility\option::set($option_data, true);
	}

}
?>