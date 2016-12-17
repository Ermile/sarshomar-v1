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
		step::plus();
		$txt_text = T_("To upload your questions, enter the title of your question on the first line and its other options on the next lines. Notice that a valid question must contain at least one title and two answers.");
		$result   =
		[
		'text'         => $txt_text ."\n#create",
		"response_callback" => utility::response_expire('create'),
		'reply_markup' => [
			"inline_keyboard" => [
				[
					[
						"text" => T_("Cancel"),
						"callback_data" => 'poll/discard'
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
		preg_match("/^type_(.*)$/", $_question, $file_content);
		if($file_content && array_key_exists('caption', bot::$hook['message']))
		{
			$_question = bot::$hook['message']['caption'];
		}
		$question = $_question;
		// $question = htmlentities($question);
		$question = markdown_filter::tag($question);
		$question = markdown_filter::remove_external_link($question);
		$question = markdown_filter::line_trim($question);
		$question_export = preg_split("[\n]", $question);

		$poll_draft = session::get('poll');
		if($poll_draft)
		{
			$poll_answers = (array) session::get('poll', "answers");
			$poll_answers = array_merge($poll_answers, $question_export);
			session::set('poll', "answers", $poll_answers);
		}
		else
		{
			$poll_title 	= $question_export[0];
			$poll_answers 	= array_slice($question_export, 1);
			session::set('poll', "title", $poll_title);
			session::set('poll', "answers", $poll_answers);
		}
		if($file_content && bot::$hook['message'][$file_content[1]])
		{
			session::set('poll', 'type', $file_content[1]);
			session::set('poll', 'file_id', bot::$hook['message'][$file_content[1]][0]['file_id']);
		}
		return self::make_draft();
	}
	public static function make_draft($_maker = false)
	{
		$poll_title 	= session::get('poll', 'title');
		$poll_answers 	= session::get('poll', 'answers');
		if(count($poll_answers) > 0)
		{
			$poll = ['title' => $poll_title];

			$poll_result = [];
			foreach ($poll_answers as $key => $value) {
				$k = ""+ ($key +1);
				$poll_result[$k] = [
				'text' 		=> $value,
				'key' 		=> $k,
				'type' 		=> 'text',
				'valid' 	=> 0,
				'invalid' 	=> 0,
				'sum' 		=> 0
				];
			}
			$maker = new make_view(bot::$user_id, $poll);
			$maker->message->add('sucsess', T_("Your question uploaded successfully.") ."\n");
			$maker->message->add_title(false);
			$maker->message->set_poll_list($poll_result);
			$maker->message->add_poll_list(null, false);
			$inline_keyboard = [[
					utility::inline(T_("Discard"), 'poll/discard'),
					]];
			if(count($poll_answers) > 1)
			{
				$inline_keyboard[0][] = utility::inline(T_("Save"), 'poll/save');
			}
			else
			{
				$maker->message->add('warn', T_("answers's poll min limit is 2, send secound answers."));
			}
		}
		else
		{
			$poll = ['title' => $poll_title];
			$maker = new make_view(bot::$user_id, $poll);
			$maker->message->add('sucsess', T_("Your question uploaded successfully."));
			$maker->message->add_title(false);
			$inline_keyboard[0][] = utility::inline(T_("Discard"), 'poll/discard');
		}

		if($_maker)
		{
			call_user_func_array($_maker, [$maker]);
		}
		$maker->message->add('hashtag', '#'.preg_replace('[\s]', '_', T_('Create new poll')));
		$txt_text = $maker->message->make();
		$result = [
			'text' 						=> $txt_text,
			"response_callback" 		=> utility::response_expire('create'),
			'parse_mode' 				=> 'HTML',
			'disable_web_page_preview' 	=> true,
			'reply_markup' 				=> [
				'inline_keyboard' 		=> $inline_keyboard
			]
		];
		$type = session::get('poll', 'type');
		$file_id = session::get('poll', 'file_id');
		// if($type)
		// {
		// 	$result['caption'] = stripslashes($result['text']);
		// 	unset($result['text']);
		// 	$result['method'] = 'send'.ucfirst($type);
		// 	$result[$type] = ucfirst($file_id);
		// }
		return $result;
	}
}
?>