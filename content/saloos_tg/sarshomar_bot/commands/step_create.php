<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;

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
			$poll = new make_view(session::get('poll'));
			$poll = $poll->query_result;
			if(count($poll['answers']) == 1)
			{
				$type = $poll['answers'][0]['type'];
				session::set('poll_options', 'type', $type);
			}
			elseif(count($poll['answers']) > 1)
			{
				session::set('poll_options', 'type', 'select');
			}
			elseif(count($poll['answers']) == 0)
			{
				session::remove('poll_options', 'type');
			}
			return self::make_draft(session::get('poll'));
		}
		return self::step1();
	}


	public static function step1()
	{
		step::plus();
		session::remove('poll');
		session::remove('poll_options');
		return callback_query\create::home();
	}



	/**
	 * [step2 description]
	 * @param  [type] $_question [description]
	 * @return [type]            [description]
	 */
	public static function step2($_question)
	{
		if(substr($_question, 0, 1) == '/')
		{
			\lib\main::$controller::clear_back_temp();
			callback_query\create::cancel();
			bot::sendResponse([
				'text' => T_("Cancel"),
				'reply_markup' => menu::main(true)
				]);
			return handle::exec(bot::$cmd, false, true);
		}
		preg_match("/^type_(.*)$/", $_question, $file_content);
		$get_poll = session::get('poll');

		$poll_request = [];

		if($get_poll)
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => ['id' => $get_poll]]);
			$get_poll = \lib\main::$controller->model()->poll_get();
			$poll_request['id'] = $get_poll['id'];
			if(count($get_poll['answers']) == 1 &&
				($get_poll['answers'][0]['type'] == 'like' || $get_poll['answers'][0]['type'] == 'descriptive')
				)
			{
				session::set('poll_options', 'type', $get_poll['answers'][0]['type']);
			}
		}

		if(count($file_content) > 0 && $get_poll && count($get_poll['answers']) > 0)
		{
			return self::make_draft($get_poll, function($_maker){
				$_maker->message->add("insert", T_("Answer type not valid"), 'before', 'hashtag');
			});
		}


		if(
			is_null(session::get('poll_options', 'type')) &&
			$get_poll &&
			count($get_poll['answers']) == 0 &&
			!is_null($get_poll['title']) &&
			$get_poll['title'] != ""
		)
		{
			return self::make_draft($get_poll, function($_maker) use($get_poll){
				// $text = T_("Please select the type of your poll from the options below");
				// if(!isset($get_poll['file']))
				// {
				// 	$text .= "\n";
				// 	$text .= T_("You can also attach a file to your poll and type the question in the next step.");
				// }
				// $_maker->message->add("insert", $text, 'before', 'hashtag');
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

				if(isset($get_poll['title']) && !is_null($get_poll['title']) && $get_poll['title'] != "")
				{
					if(self::is_type('like') || self::is_type('descriptive'))
					{
						$poll_request['description'] = join($question_export, "\n");
					}
					else
					{
						$poll_request['answers'] = isset($get_poll['answers']) ? $get_poll['answers'] : [];
						$poll_answers 	= $question_export;
					}

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
				if(count($poll_answers) > 0)
				{
					session::set('poll_options', 'type', "select");
				}
			}

			if($poll_answers)
			{
				foreach ($poll_answers as $key => $value) {
					$poll_request['answers'][] = ['title' => $value, 'type' => 'select'];
				}
			}
			else
			{
				unset($poll_request['answers']);
			}
			$poll_request['language'] = callback_query\language::check(true);

			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
			$add_poll = \lib\main::$controller->model()->poll_add(['method' => $get_poll ? 'patch' : 'post']);
			if(\lib\debug::$status)
			{
				session::set('poll', $add_poll['id']);
			}
			elseif(\lib\debug::$status == 0)
			{
				step::stop();
				return [
					'text' => \lib\debug::compile()['messages']['error'][0]['title'],
					'reply_markup' => menu::main(true)
				];
			}
			return self::make_draft($add_poll['id']);
		}
	}

	public static function upload_file($_file_link)
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
			$add_poll = \lib\main::$controller->model()->poll_add(['method' => 'patch']);
		return $file_uploaded;
	}

	public static function make_draft($_poll, $_maker = false)
	{
		$maker = new make_view($_poll);
		$title = $maker->query_result['title'];
		if(!$maker->query_result['title'])
		{
			$maker->query_result['title'] = T_('Please enter question title');
		}
		$poll_id = $maker->query_result['id'];
		if(isset($maker->query_result['title']) && !empty($maker->query_result['title']) && empty($maker->query_result['answers']) && !session::get('poll_options' , 'type'))
		{
			$maker->message->add('sucsess', T_("Your question uploaded successfully."));
		}
		$maker->message->add_title();
		$maker->message->add_poll_list(false, false);

		if(session::get('poll_options' , 'type') == 'select' && !empty($maker->query_result['title']))
		{
			$count = ['first', 'second', 'third'];
			$count_answer = count($maker->query_result['answers']);
			if($count_answer > 2)
			{
				if($count_answer > 20 && substr($count_answer, -1) == 0)
				{
					$count = ($count_answer +1) .'st';
				}
				elseif($count_answer > 20 && substr($count_answer, -1) == 1)
				{
					$count = ($count_answer +1) .'nd';
				}
				elseif($count_answer > 20 && substr($count_answer, -1) == 2)
				{
					$count = ($count_answer +1) .'rd';
				}
				else
				{
					$count = ($count_answer +1) .'th';
				}
			}
			else
			{
				$count = $count[$count_answer];
			}
			$maker->message->add('insert', T_("Enter the text of :count option", ['count' => $count]));
		}

		if(self::is_type('like') || self::is_type('descriptive'))
		{
			$maker->message->add('description', "\n" . T_("You can add a subject as the content of your poll"), 'after', 'poll_list');
		}


		if(!session::get('poll_options' , 'type'))
		{
			$maker->message->add('insert', T_("Please select the type of your poll from the options below"));
			if(!isset($maker->query_result['file']))
			{
				$maker->message->add('file_upload', T_("You can also attach a file to your poll and type the question in the next step."));
			}
			$maker->inline_keyboard->add([
					[
						"text" => T_("Selective") . (self::is_type('selective') ? " ✅" : ""),
						"callback_data" => 'create/type/select'],
					// [
					// 	"text" => T_("Emoji") . (self::is_type('emoji') ? " ✅" : ""),
					// 	"callback_data" => 'create/type/emoji']
				]);
			$maker->inline_keyboard->add([
					[
						"text" => T_("Like") . (self::is_type('like') ? " ✅" : ""),
						"callback_data" => 'create/type/like'],
					[
						"text" => T_("Descriptive") . (self::is_type('descriptive') ? " ✅" : ""),
						"callback_data" => 'create/type/descriptive']
				]);
		}
		if(isset($maker->query_result['answers'][0]) && !is_null($title))
		{
			$maker->inline_keyboard->add([
				['text' => T_('Publish'), 'callback_data' => 'poll/status/publish/' . $maker->query_result['id']]
			]);
			if(isset($maker->query_result['access_profile']))
			{
				$maker->inline_keyboard->add([
					['text' => T_("Do not submit respondent's information"), 'callback_data' => 'create/access_profile/remove/' . $maker->query_result['id']]
				]);
			}
			else
			{
				$maker->inline_keyboard->add([
					['text' => T_("Submit respondent's information"), 'callback_data' => 'create/access_profile/add/' . $maker->query_result['id']]
				]);
			}
			$maker->inline_keyboard->add([
				[
					'text' => T_('Edit'),
					'url' => 'https://' . $_SERVER['SERVER_NAME'] . '/' . $maker->query_result['language'] . '/$/' . $maker->query_result['id']
					],
				['text' => T_('Main menu'), 'callback_data' => 'create/close']
			]);
		}
		else
		{
			$maker->inline_keyboard->add([
					['text' => T_('Main menu'), 'callback_data' => 'create/close']
				]);
		}

		$maker->message->add('hashtag', utility::tag(T_("Create new poll")));

		if(is_object($_maker))
		{
			$_maker($maker);
		}

		$txt_text = $maker->message->make();



		$inline_keyboard = $maker->inline_keyboard->make();

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

	public static function is_type($_type)
	{
		if(session::get('poll_options', 'type') === $_type)
		{
			return true;
		}
		return false;

	}
}
?>