<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class step_sarshomar
{
	private static $menu         = ["hide_keyboard" => true];
	private static $lastQuestion = null;
	private static $lastAnswers  = null;

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
				return self::stop(true);
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
		// get and set last question
		$questionExist = self::getLastQuestion();
		if(!$questionExist)
		{
			return step_subscribe::start("شما به همه سوالات پاسخ دادید!\n");
		}
		// go to next step
		step::plus();
		// set title for
		step::set('textTitle', 'question');
		// increase custom number
		step::plus(1, 'i');
		// create output text
		$txt_text .= step::get('question');
		$txt_text .= self::answersKeyboard(false);
		// $txt_text .= "[لینک دسترسی مستقیم به این نظرسنجی](telegram.me/sarshomar_bot?start=poll_123)";
		$txt_text .= "/cancel عدم تمایل به ادامه پاسخ‌دهی\n";

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
	 * @param  [type] $_answer_txt [description]
	 * @return [type]            [description]
	 */
	public static function step4($_answer_txt)
	{
		$answersList = self::answersKeyboard(true);
		if(!$answersList)
		{
			return false;
		}
		// if user add answer in command format
		if(substr($_answer_txt, 0, 1) === '/')
		{
			$useCommand = true;
			$cmdInput   = substr($_answer_txt, 1);

			if(isset($answersList[$cmdInput]))
			{
				$_answer_txt = $answersList[$cmdInput];
			}

		}
		if($answer_id = array_search($_answer_txt, $answersList))
		{
			// go to next step
			step::plus();
			// get question id
			$question_id = step::get('question_id');
			// save answer
			\lib\db\polls::saveAnswer(bot::$user_id, $question_id, $answer_id, $_answer_txt);

			// create output text
			$txt_text = "پاسخ *سوال ". step::get('i')."*دریافت شد.\n\n";
			$txt_text .= 'سوال: '.step::get('question')."\n";
			$txt_text .= 'پاسخ شما: '.$_answer_txt;
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
			case '/next':
			case 'next':
				step::goto(3);
				return self::step3();
				break;

			case 'مشاهده نتایج':
			case 'result':
			case '/result':
				$txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
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
	public static function answersKeyboard($_onlyArray = null)
	{
		$answersList = step::get('answers');
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
		$rowMax     = 3;
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



	private static function drawKeyboard()
	{

	}


	/**
	 * get last question from database and return it
	 * @return [type] [description]
	 */
	public static function getLastQuestion($_user_id = null)
	{
		if(!$_user_id)
		{
			$_user_id = bot::$user_id;
		}

		$question = \lib\db\polls::getLast($_user_id);
		$question['question'] = html_entity_decode($question['question']);
		step::set('question_id', $question['id']);
		step::set('question', $question['question']);
		step::set('answers', $question['answers']);
		step::set('tags', $question['tags']);

		if(!is_array($question['answers']))
		{
			return false;
		}
		return true;
	}

	public static function removeUserAnswers()
	{
		$result = \lib\db\polls::removeUserAnswers(bot::$user_id);
		$result   =
		[
			[
				'text'         => 'آرشیو نظرات شما پاک شد!',
				'reply_markup' => menu::main(true),
			],
		];
		return $result;
	}
}
?>