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
	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create');
		if($_run_as_edit)
		{
			step::goingto(2);
			return self::make_draft(session::get('poll'));
		}
		return self::step1();
	}


	public static function step1()
	{
		step::plus();
		session::remove('poll');
		return callback_query\create::home();
	}



	/**
	 * [step2 description]
	 * @param  [type] $_question [description]
	 * @return [type]            [description]
	 */
	public static function step2($_question)
	{
		preg_match("/^type_(.*)$/", $_question, $file_content);
		$get_poll = session::get('poll');

		$poll_request = [];

		if($get_poll)
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => ['id' => $get_poll]]);
			$get_poll = \lib\main::$controller->model()->poll_get();
			$poll_request['id'] = $get_poll['id'];
		}

		if(count($file_content) > 0 && $get_poll && count($get_poll['answers']) > 0)
		{
			return self::make_draft($get_poll, function($_maker){
				$_maker->message->add("wrong_data", T_("Answer type not valid"), 'before', 'hashtag');
			});

		}

		if($file_content && isset(bot::$hook['message'][$file_content[1]]))
		{
			$file = bot::$hook['message'][$file_content[1]];
			if(is_array($file))
			{
				$file = end($file);
			}
			if(array_key_exists('caption', bot::$hook['message']))
			{
				$_question = bot::$hook['message']['caption'];
			}
			elseif($file_content)
			{
				$_question = '';
			}
			$file_id = $file['file_id'];
			$get_file = bot::getFile([
				'file_id' => $file_id
				]);

			$file_path = $get_file['result']['file_path'];
			$file_link = 'https://api.telegram.org/file/bot' . bot::$api_key . '/' . $file_path;
			$poll_request['file'] = $file_link;
			$upload = self::upload_file($file_link);
			if($get_poll)
			{
				$poll_request['file'] = $upload['code'];
			}
			else
			{
				\lib\utility::$REQUEST = new \lib\utility\request([
					'method' => 'array',
					'request' => [
					'id' => $upload['poll_id']
					]]);
				$get_poll = \lib\main::$controller->model()->poll_get();
				$poll_request['id'] = $get_poll['id'];
				session::set('poll', $get_poll['id']);
			}
		}


		$question = $_question;
		$question = markdown_filter::tag($question);
		$question = markdown_filter::remove_external_link($question);
		$question = markdown_filter::line_trim($question);
		$question_export = preg_split("[\n]", $question);

		if(!empty($question_export))
		{

			if($get_poll)
			{

				if(isset($get_poll['title']) && $get_poll['title'] != '')
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
			$poll_request['language'] = callback_query\language::check(true);

			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
			$add_poll = \lib\main::$controller->model()->poll_add(['method' => $get_poll ? 'put' : 'post']);
			if(\lib\debug::$status)
			{
				session::set('poll', $add_poll['id']);
			}
			return self::make_draft($add_poll['id']);
		}
	}

	public function upload_file($_file_link)
	{
		$poll_id = session::get('poll');
		if(!$poll_id)
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => []]);
			$add_poll = \lib\main::$controller->model()->poll_add(['method' => 'post']);
			$poll_id = $add_poll['id'];
		}
		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' 	=> 'array',
			'request' 	=> [
				'id'		=> $poll_id,
			]
			]);
		$file_uploaded = \lib\main::$controller->model()->upload_file(['url' => $_file_link]);
		$file_uploaded['poll_id'] = $poll_id;

			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => [
				"id" 	=> $poll_id,
				"file" 	=> $file_uploaded['code']
				]]);
			$add_poll = \lib\main::$controller->model()->poll_add(['method' => 'put']);

		return $file_uploaded;
	}

	public static function make_draft($_poll, $_maker = false)
	{
		$maker = new make_view($_poll);
		if(!$maker->query_result['title'])
		{
			$maker->query_result['title'] = T_('Please enter question title');
		}
		$poll_id = $maker->query_result['id'];
		$maker->message->add('sucsess', T_("Your question uploaded successfully."));
		$maker->message->add_title();
		$maker->message->add_poll_list();
		$maker->message->add('insert', T_("Insert next asnwer or choise action"));
		$maker->message->add('hashtag', utility::tag(T_("Create new poll")));
		if(is_object($_maker))
		{
			$_maker($maker);
		}
		$txt_text = $maker->message->make();
		$maker->inline_keyboard->add_change_status();
		$maker->inline_keyboard->add([['text' => T_('Save as draft'), 'callback_data' => 'create/close']]);
		$inline_keyboard = $maker->inline_keyboard->make();
		unset($inline_keyboard[0]);
		$inline_keyboard = array_values($inline_keyboard);
		array_unshift($inline_keyboard, [["text" => T_("Options"),"callback_data" => 'create/options']]);

		$disable_web_page_preview = true;
		if(isset($maker->query_result['file']))
		{
			$disable_web_page_preview = false;
		}

		$result = [
		'text' 						=> $txt_text,
		"response_callback" 		=> utility::response_expire('create'),
		'parse_mode' 				=> 'HTML',
		'disable_web_page_preview' 	=> $disable_web_page_preview,
		'reply_markup' 				=> [
		'inline_keyboard' 			=> $inline_keyboard
		]
		];
		return $result;
	}
}
?>