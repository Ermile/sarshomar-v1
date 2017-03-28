<?php
namespace content\saloos_tg\sarshomarbot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\markdown_filter;
use \content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\main;
use \lib\debug;

class step_create
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create');
		if($_run_as_edit)
		{
			$maker = new make_view(session::get('poll'));
			if(!empty($maker->query_result['answers']))
			{
				switch ($maker->query_result['answers'][0]['type']) {
					case 'select':
						step::stop();
						return step_create_select::start();
						break;

					default:
						return self::step1();
						break;
				}
			}
			elseif(isset($maker->query_result['file']) && !empty($maker->query_result['file']))
			{
				step::goingto(4);
				return slef::step4();
			}
			else
			{
				return self::step1();
			}
		}
		session::remove('poll');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		if(is_null($_text))
		{
			return callback_query\create::home();
		}
		$request = utility::make_request(['title' => $_text]);
		$add_poll = main::$controller->model()->poll_add(['method' => 'post']);
		$poll_id = $add_poll['id'];
		session::set('poll', $poll_id);

		if(debug::$status === 0) return self::error();

		step::plus();
		return self::step2();
	}

	public static function step2($_text = null)
	{
		$maker = new make_view(session::get('poll'));
		$maker->message->add_title();
		$maker->message->add('status', "\n" . "📍 " . T_("Does your question include multimedia content including image, movie, audio or file?"));
		$maker->message->add('tag', utility::tag(T_("Create new poll")));
		$maker->inline_keyboard->add([
				[
					"text" => T_("Yes, send file"),
					"callback_data" => 'create/upload_file',
				],
				[
					"text" => T_("No"),
					"callback_data" => 'create/choise_type'
				]
			]);
		$maker->inline_keyboard->add([
				[
					"text" => T_("Cancel"),
					"callback_data" => 'create/cancel'
				]
			]);
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');

		return $return;
	}

	public static function step3($_text = null)
	{
		$poll_id = session::get('poll');
		preg_match("/^type_(.*)$/", $_text, $file_content);
		if($file_content && isset(bot::$hook['message'][$file_content[1]]))
		{
			$file = bot::$hook['message'][$file_content[1]];
			if(!isset($file['file_id']))
			{
				$file = end($file);
			}
			$file['method'] = $file_content[1];
			bot::sendResponse([
				'text' => T_("Receiving and processing file..."),
				'method' => 'sendMessage'
				]);

			bot::sendResponse([
				'action' => 'upload_'.$file_content[1],
				'method' => 'sendChatAction'
				]);

			$file_id = $file['file_id'];
			$get_file = bot::sendResponse(['method'=>'getFile', 'file_id' => $file_id]);
			$file_path = $get_file['result']['file_path'];
			$file_link = 'https://api.telegram.org/file/bot' . bot::$api_key . '/' . $file_path;

			utility::make_request(['id'	=> $poll_id]);
			$file_uploaded = \lib\main::$controller->model()->upload_file(['url' => $file_link]);

			if(debug::$status === 0)
			{
				return callback_query\create::upload_file(null, null, self::error());
			}

			$meta = \lib\db\options::get([
				'option_cat' => 'telegram',
				'option_key' => 'file_uploaded_'.$file_uploaded['code'],
				'option_value' => $file['file_id'],
				'limit'	=> 1
			]);
			if(empty($meta) || is_null($meta))
			{
				\lib\db\options::insert([
					'option_cat' => 'telegram',
					'option_key' => 'file_uploaded_'.$file_uploaded['code'],
					'option_value' => $file['file_id'],
					'option_meta' => json_encode($file)
				]);
			}
			utility::make_request(["id" => $poll_id,"file" => $file_uploaded['code']]);
			\lib\main::$controller->model()->poll_add(['method' => 'patch']);

			if(debug::$status === 0)
			{
				return callback_query\create::upload_file(null, null, self::error());
			}

			step::plus();
			return self::step4();
		}
		return callback_query\create::upload_file();
	}

	public static function step4($_text = null)
	{
		$maker = new make_view(session::get('poll'));
		$maker->message->add_title();
		$maker->message->add('status', "\n" . "📍 " . T_("Please select the type of your poll from the options below."));
		$maker->message->add('tag', utility::tag(T_("Create new poll")));
		$maker->inline_keyboard->add([
					[
						"text" => T_("Multiple-choice"),
						"callback_data" => 'create/type/select'
					]
				]);
		$maker->inline_keyboard->add([
				[
					"text" => T_("Like"),
					"callback_data" => 'create/type/like'
				],
				[
					"text" => T_("Emoji"),
					"callback_data" => 'create/type/emoji'
				]
			]);
		$maker->inline_keyboard->add([
				[
					"text" => T_("Descriptive"),
					"callback_data" => 'create/type/descriptive'
				]
			]);
		$maker->inline_keyboard->add([
				[
					"text" => T_("Cancel"),
					"callback_data" => 'create/cancel'
				]
			]);
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');

		return $return;
	}

	public static function error()
	{
		debug::$status = 1;
		return debug::compile()['messages']['error'][0]['title'];
	}
}
?>