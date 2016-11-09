<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\handle;

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
	public static function start($_text = null, $_skip = null)
	{
		if(is_null($_text))
		{
			$return = [
				"text" => "Choice poll type.",
				"reply_markup" => [
					'inline_keyboard' => [
						[
							[
								"text" 			=> T_("Sarshomar"),
								"callback_data" => "ask/type/sarshomar"
							],
							[
								"text" 			=> T_("Premium"),
								"callback_data" => "ask/type/premium"
							]
						]
					]
				],
				"response_callback" => utility::response_expire('sarshomar')
			];
		}
		else
		{
			$return = self::step1($_text);
		}
		return $return;
	}


	/**
	 * get list of questions and ask a question
	 * @return [type] [description]
	 */
	public static function step1($_poll_url = null)
	{
		$is_custom_poll = false;
		$poll_url = $_poll_url;
		if(!is_null($poll_url))
		{
			$poll_id = \lib\utility\shortURL::decode($poll_url);
			$is_answered = \lib\db\answers::is_answered(bot::$user_id, $poll_id);
			if($is_answered)
			{
				return callback_query\ask::get_poll_result($poll_url, $poll_id, 0);
			}
			$questionExist = self::get_question(null, $poll_id);
			$is_custom_poll = true;
			step::set('custom_link', true);
		}
		else
		{
			$questionExist = self::get_question();
		}
		// fix limit of number of answered in a period of time
		$answeredLimit   = \lib\db\stat_polls::answeredInPeriod(bot::$user_id, 6);
		if(!$questionExist)
		{
			if(!$is_custom_poll)
			{
				return step_subscribe::start("شما به همه سوالات پاسخ دادید!\n");
			}
			else
			{
				return ["text" => "چنین سوالی موجود نمی‌باشد!\n"];

			}
		}
		if($answeredLimit >= 6)
		{
			$txt = "محدودیت پاسخ‌دهی در هر بار به اتمام رسید!\n";
			$txt .= "در حال حاضر هر ۶ ساعت امکان پاسخ‌دهی به ۶ سوال وجود دارد.";
			return step_subscribe::start($txt);
		}
		// go to next step, step4

		// set title for
		step::set('textTitle', 'question');
		// reset last answer
		step::set('lastAnswer', null);
		// increase custom number
		step::plus(1, 'i');
		// create output text

		$result   =
		[
			'text'         => poll_result::get_message(step::get('poll_message')),
			'reply_markup' => ["inline_keyboard" => step::get("poll_inline_keyboard")],
			'parse_mode' => 'Markdown',
			'disable_web_page_preview' => true,
			"response_callback" => utility::response_expire('ask')
		];
		// step::stop();
		// return menu
		return $result;
	}



	/**
	 * [step2 description]
	 * @param  [type] $_answer_txt [description]
	 * @return [type]            [description]
	 */
	public static function step2($_answer_txt)
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

		// get answer id from answers list
		$answer_id = array_search($_answer_txt, $answersList);
		if($_answer_txt === '/skip')
		{
			$answer_id = -1;
		}
		if(is_int($answer_id))
		{
			// go to next step
			step::plus();
			// save last answer
			step::set('lastAnswer', $_answer_txt);
			// get question id
			$question_id = step::get('question_id');
			// save answer
			\lib\db\answers::save(bot::$user_id, $question_id, $answer_id, ['answer_txt' => $_answer_txt]);
			// create output text
			// $txt_text = "پاسخ *سوال ". step::get('i')."*دریافت شد.\n\n";
			// $txt_text .= 'سوال: '.step::get('question')."\n";
			// $txt_text .= 'پاسخ شما: '.$_answer_txt;
			$link = 'https://sarshomar.com/sp_';
			$link .= \lib\utility\shortURL::encode($question_id);

			$txt_text = self::showResult(true);
			$menu =
			[
				'inline_keyboard' =>
				[
					[
						[
							'text' => 'مشاهده نمودار و بررسی نتایج 🌐',
							'url'  => $link,
						],
						// [
						// 	'text'                => 'search \'test\' inline',
						// 	'switch_inline_query' => 'test'
						// ],
					]
				],
				// 'keyboard' =>
				// [
				// 	["سوال بعدی"],
				// 	// ["مشاهده نتایج"],
				// 	[T_('🔙 Back')],
				// ],
			];
			// get name of question
			$result   =
			[
				[
					'text'         => $txt_text,
					// 'reply_markup' => null,
					'reply_markup' => $menu,
				],
			];

			// got to step1
			step::goingto(1);
			// show new question, get from step3
			if(!step::get("custom_link")){
				$result[] = self::step1(null);
			}
			else
			{
				step::set("custom_link", false);
				$text = 'ممنون، ما به نظر شما احترام می‌گذاریم.';
				if($answer_id == -1)
				{
					$text = "ممنون، احتمالا دوباره این پرسش را نخواهید دید.";
				}
				$result = [
				[
				"text" => $text,
				'reply_markup' => menu::main(true)
				]
				];
			}
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
	public static function step3($_item)
	{
		// create output text
		$txt_text = "سوال ". step::get('i')."\n\n";
		switch ($_item)
		{
			case 'سوال بعدی':
			case '/next':
			case 'next':
				step::goingto(1);
				return self::step1();
				break;

			case 'مشاهده نتایج':
			case 'result':
			case '/result':
				$txt_text = self::showResult(true);
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case 'resultRaw':
			case '/resultRaw':
				$txt_text = self::showResult(false);
				// $txt_text = 'بزودی نتایح تهیه و نمایش داده می‌شوند:)';
				break;

			case '🔙':
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



	private static function drawKeyboard()
	{

	}


	/**
	 * get last question from database and return it
	 * @return [type] [description]
	 */
	public static function get_question($_user_id = null, $_poll_id = null)
	{
		if(!$_user_id)
		{
			$_user_id = bot::$user_id;
		}

		if(is_null($_poll_id))
		{
			$question = \lib\db\polls::get_last($_user_id);
			$poll_result = poll_result::make($question, ['callback_data' => function($_callback_data){
				return $_callback_data .'/last';
			}]);
		}
		else
		{
			$question = \lib\db\polls::get_poll($_poll_id);
			$poll_result = poll_result::make($question);
		}

		if(empty($question))
		{
			return false;
		}

		$question['question'] = html_entity_decode($question['content']);
		step::set('question_id', $question['id']);
		step::set('questionRaw', $question['content']);
		step::set('question', $question['question']);
		step::set("poll_message", $poll_result['message']);
		step::set("poll_inline_keyboard", $poll_result['inline_keyboard']);

		$answers = [];
		foreach ($question['meta']['opt'] as $key => $value) {
			$answers[$key] = $value['txt'];
		}
		step::set('answers', $answers);
		// step::set('tags', $question['tags']);

		if(!is_array($question['meta']['opt']))
		{
			return false;
		}
		return true;
	}

	private static function showResult($_percentage = false, $_question_id = null, $_question = null, $_userAnswer = null)
	{
		if(!$_question_id)
		{
			$_question_id = step::get('question_id');
		}
		if(!$_question)
		{
			$_question = step::get('question');
		}
		if(!$_userAnswer)
		{
			$_userAnswer = step::get('lastAnswer');
		}
		$result       = \lib\db\stat_polls::get_telegram_result($_question_id, 'count', 'txt');
		arsort($result);
		$result_count = array_sum($result['result']);
		if(!$result_count)
		{
			$result_count = 1;
		}
		$output       = "📊 ".$_question."\n";
		foreach ($result['result'] as $key => $value)
		{
			$percent      = ($value * 100) / $result_count;
			$percent      = (int)round($percent);
			if($_percentage)
			{
				$result[$key] = $percent;
			}
			// add key into output
			$maxCharOnLine = 40;
			$itemLenght    = mb_strlen($key);
			$percent_10    = round($percent/10);
			$resultLine    = $key;
			if($_userAnswer === $key)
			{
				$resultLine .= "🚩";

			}
			$resultLine    .= "\n";
			// $resultLine    .= str_repeat('👍', $percent_10);
			$resultLine    .= str_repeat('⬛️', $percent_10);
			$resultLine    .= str_repeat('⬜️', 10 - $percent_10);
			$resultLine    .= " `$percent%`";

			$output .= $resultLine . "\n";
		}
		if($result_count > 10)
		{
			// $output       .= "*". $result_count. "* نفر به این سوال پاسخ داده‌اند\n";
			$output       .= "👥 *". $result_count. "* نفر \n";
		}

		// $output .= "[لینک مستقیم این نظرسنجی](telegram.me/sarshomarbot?start=poll_$_question_id)";

		return $output;
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