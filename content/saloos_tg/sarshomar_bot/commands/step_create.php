<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use content\saloos_tg\sarshomar_bot\commands\make_view;

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
		session::remove('poll');
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
		$get_poll = '3KZZh';//session::get('poll');

		if($get_poll)
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => ['id' => $get_poll]]);
			$get_poll = \lib\main::$controller->model()->get_poll();
			$maker = new make_view();
		}

		if($file_content && array_key_exists('caption', bot::$hook['message']))
		{
			$_question = bot::$hook['message']['caption'];
		}
		elseif($file_content)
		{
			$_question = '';
		}
		if(count($file_content) > 0 && $get_poll)
		{
			return self::make_draft($get_poll, function($_maker){
				$_maker->message->add("wrong_data", T_("Answer type not valid"), 'before', 'hashtag');
			});

		}
		$question = $_question;
		$question = markdown_filter::tag($question);
		$question = markdown_filter::remove_external_link($question);
		$question = markdown_filter::line_trim($question);
		$question_export = preg_split("[\n]", $question);

		if(!empty($question_export[0]))
		{
			$poll_request = [];

			if($get_poll)
			{
				$poll_request['id'] = $get_poll['id'];

				if(isset($get_poll['title']))
				{
					$poll_request['answers'] = isset($get_poll['answers']) ? $get_poll['answers'] : [];
					$poll_answers 	= $question_export;

				}
				else
				{
					$poll_request['title'] = $question_export[0];
					$poll_answers 	= array_slice($question_export, 1);
					if($poll_answers)
					{
						$poll_request['answers'] = [];
					}
				}
			}
			else
			{
				$poll_request['title'] = $question_export[0];
				$poll_answers 	= array_slice($question_export, 1);
			}

			if($poll_answers)
			{
				foreach ($poll_answers as $key => $value) {
					$poll_request['answers'][] = ['title' => $value, 'type' => 'select'];
				}
			}
			handle::send_log_clear();
			handle::send_log($poll_request);
			return ['text' => $_question];

			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
			$add_poll = \lib\main::$controller->model()->add_poll(null);
			if(\lib\debug::$status)
			{
				session::set('poll', $add_poll['id']);
			}
		}

		if(!session::get('poll', 'file_link') && $file_content && bot::$hook['message'][$file_content[1]])
		{
			$file = bot::$hook['message'][$file_content[1]];
			$file = isset($file['file_id']) ? $file : end($file);
			$file_id = $file['file_id'];
			$get_file = bot::getFile([
				'file_id' => $file_id
				]);
			$file_link = 'https://api.telegram.org/file/bot' . bot::$api_key . '/' . $get_file['result']['file_path'];
			$file_server_addr = \lib\utility\upload::temp_donwload($file_link);
			session::set('poll', 'file_link', $file_server_addr->get_link());
			session::set('poll', 'file_addr', $file_server_addr->get_new_name());
			session::set('poll', 'file_type', $file_content[1]);
		}
		return self::make_draft($get_poll);
	}
	public static function make_draft($_poll, $_maker = false)
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
			if(count($poll_answers) >= 1)
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
			$poll = ['title' => is_null($poll_title)? T_('Please enter question title') : $poll_title];
			$maker = new make_view(bot::$user_id, $poll);
			$maker->message->add('sucsess', T_("Your question uploaded successfully."));
			$maker->message->add_title(false);
			$inline_keyboard[0][] = utility::inline(T_("Discard"), 'poll/discard');
		}
		$file = session::get('poll', 'file_link');
		if($file)
		{
			$type = session::get('poll', 'file_type');
			switch ($type) {
				case 'photo':
					$emoji = '🖼';
					break;
				case 'video':
					$emoji = '📹';
					break;
				case 'audio':
					$emoji = '🔊';
					break;

				default:
					$emoji = '📝';
					break;
			}
			$maker->message->add('file', $emoji . utility::link($file, 'File'), 'after', 'title');
		}


		$maker->message->add('hashtag', '#'.preg_replace('[\s]', '_', T_('Create new poll')));
		if($_maker)
		{
			call_user_func_array($_maker, [$maker]);
		}
		$txt_text = $maker->message->make();
		$result = [
		'text' 						=> $txt_text,
		"response_callback" 		=> utility::response_expire('create'),
		'parse_mode' 				=> 'HTML',
		// 'disable_web_page_preview' 	=> true,
		'reply_markup' 				=> [
		'inline_keyboard' 		=> $inline_keyboard
		]
		];

		//
		//📸
		//🖼
		//
		// $file_id = session::get('poll', 'file_id');
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