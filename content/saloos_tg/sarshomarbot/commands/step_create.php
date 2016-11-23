<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\markdown_filter;
use content\saloos_tg\sarshomarbot\commands\make_view;

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
		step::plus(1);
		$txt_text = "سوال نظرسنجی را در خط اول وارد نمایید\n";
		$txt_text .= "پاسخ‌های خود را به ترتیب در هر خط قرار دهید\n";
		$txt_text .= "هر نظرسنجی باید حداقل دو پاسخ داشته باشد\n";
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
			$txt_text = "نظرسنجی که ثبت کردید به صورت زیر می‌باشد: \n\n";
			$poll_title 		= $question_export[0];
			$poll_answers 	= array_slice($question_export, 1);
			$poll_id 		= utility::microtime_id();
			session::set('poll', $poll_id, ["title" => $poll_title, "answers" => $poll_answers]);

			$poll = ['title' => $poll_title];
			foreach ($poll_answers as $key => $value) {
				$poll['meta']['opt'][] = ["txt" => $value];
			}
			$maker = new make_view(bot::$user_id, $poll);
			$maker->message->add_title(false);
			$maker->message->add_poll_list(null, false);
			$txt_text .= $maker->message->make();
			$txt_text .= "\nقصد دارید انتشار دهید یا حذف کنید؟";
			$result = [
				'text' 						=> $txt_text,
				'parse_mode' 				=> 'Markdown',
				'disable_web_page_preview' 	=> true,
				'reply_markup' 				=> [
					'inline_keyboard' 		=> [
						[
						utility::inline(T_("Discard"), 'poll/discard/'.$poll_id),
						utility::inline(T_("Publish"), 'poll/publish/'.$poll_id)
						]
					]
				]
			];
			$markup = null;
		}
		step::stop();
		return $result;
	}
}
?>