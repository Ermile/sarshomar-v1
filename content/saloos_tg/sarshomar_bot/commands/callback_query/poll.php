<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomar_bot\commands\menu;

class poll
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 1)
		{
			$method = $_data_url[1];
			$return = self::$method($_query, $_data_url);
		}
		if(is_array($return))
		{
			return $return;
		}
		return [];
	}

	public static function list($_query, $_data_url)
	{
		$message_per_page = 3;
		if(is_null($_query))
		{
			$page = 1;
			$start = 0;
		}
		else
		{
			$page = (int) $_data_url[2];
			$start = (($page-1) * $message_per_page);
		}


		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' 	=> 'array',
			'request' => [
				'in' 		=> 'me',
				'status'	=> 'stop pause publish draft awaiting',
				'language'	=> 'en fa ar',
				'from'  	=> (int) $start,
				'to'  		=> (int) ($start + $message_per_page),
			]
			]);
		$search = \lib\main::$controller->model()->poll_search(true);

		$query_result = $search['data'];

		$total_page = ceil($search['total'] / $message_per_page);

		if(empty($query_result))
		{
			$message = T_("You didn't add any poll");
			$message = "\n";
			$message = T_("Do you like to add poll");
			$inline_keyboard =  [
				[["text" => T_("Add new poll"), "callback_data" => "poll/new"]]
			];
		}
		else
		{
			$message = utility::nubmer_language($page . "/" . $total_page) . "\n";
			foreach ($query_result as $key => $value) {
				$message .= $value['title'];
				$message .= utility::nubmer_language("($value[count_vote])");
				$message .= ' - ' . T_(ucfirst($value['status']));
				$message .= "\n";
				$message .= "/" . $value['id'];
				$message .= "\n\n";
			}
		}
		$return = ['text' => $message];
		if($total_page > 1)
		{
			if($page > 2)
			{
				$inline_keyboard[0][] = ["text" => T_("First"), "callback_data" => "poll/list/1"];
			}
			if($page > 1)
			{
				$inline_keyboard[0][] = ["text" => T_("Back"), "callback_data" => "poll/list/" . ($page-1)];
			}



			if($page < $total_page)
			{
				$inline_keyboard[0][] = ["text" => T_("Next"), "callback_data" => "poll/list/" . ($page+1)];
			}

			if(($page + 1) < $total_page || $page == 1)
			{
				$inline_keyboard[0][] = ["text" => T_("Last"), "callback_data" => "poll/list/" . $total_page];
			}
		}
		if(isset($inline_keyboard))
		{
			$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
		}
		$return['parse_mode'] = "HTML";
		if(is_null($_query))
		{
			return $return;
		}
		callback_query::edit_message($return);
	}

	public static function answer_descriptive($_query, $_data_url)
	{
		list($class, $method, $status) = $_data_url;
		step::stop();

		if($status != 'answer')
		{
			bot::sendResponse([
				'text' 						=> utility::tag(T_('لغو پاسخ‌دهی')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
				]);
			return ;
		}
		$poll_id = session::get('answer_descriptive', 'id');
		$text = session::get('answer_descriptive', 'text');
		$request = [
			'id' => $poll_id,
			'descriptive' => $text,
		];
		session::remove('answer_descriptive');


		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $request]);
		$add_poll = \lib\main::$controller->model()->poll_answer_add(['method' => 'post']);

		$debug_status = \lib\debug::$status;
		$debug = \lib\debug::compile();

		\lib\debug::$status = 1;

		if(!$debug_status)
		{
			bot::sendResponse([
				'text' 						=> utility::tag(T_('ثبت پاسخ با خطا مواجه شد')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
			]);
			return ['text' => '❗️' . $debug['messages']['error'][0]['title']];
		}
		else
		{
			session::remove_back('expire', 'inline_cache', 'answer_descriptive');
			session::remove('expire', 'inline_cache', 'answer_descriptive');
			$maker = new make_view($poll_id);
			$maker->message->add_title();
			$maker->message->message['title'] = '❔ ' . $maker->message->message['title'];
			$maker->message->add('answer' , '📝' . $text);
			$maker->message->add('answer_line' , "");
			$maker->message->add('answer_verify' , '✅ ' . T_("پاسخ شما ثبت شد"));
			$maker->message->add('tag' ,  utility::tag(T_("ارسال پاسخ")));
			bot::sendResponse([
				'text' 						=> utility::tag(T_('پاسخ شما ثبت شد')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
			]);
			callback_query::edit_message($maker->make());
		}
		return ['text' => \lib\debug::compile()['title']];
	}

	public static function deny_answer($_query, $_data_url)
	{

	}

	public static function answer($_query, $_data_url)
	{
		// \lib\db::transaction();
		\lib\storage::set_disable_edit(true);
		if(count($_data_url) == 4)
		{
			list($class, $method, $poll_id, $answer) = $_data_url;
			$last = null;
		}elseif (count($_data_url) == 5) {
			list($class, $method, $poll_id, $answer, $last) = $_data_url;
		}


		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' 	=> 'array',
			'request' => [
				'id' 		=> $poll_id,
			]
			]);
		\lib\debug::$status = 1;
		$get_answer = \lib\main::$controller->model()->poll_answer_get([]);
		\lib\debug::$status = 1;

		$request = ['id' => $poll_id];

		switch ($answer) {
			case 'like':
				$request['like'] = true;
				break;
			case 'skip':
				$request['skip'] = true;
				break;
			default:
				$request['answer'] = [$answer => true];
				break;
		}

		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $request]);


		$add_poll = \lib\main::$controller->model()->poll_answer_add([
			'method' => in_array('edit', $get_answer['available']) ? 'put' : 'post'
			]);

		$debug_status = \lib\debug::$status;
		$debug = \lib\debug::compile();

		\lib\debug::$status = 1;
		callback_query::edit_message(ask::make(null, null, [
			'poll_id' 	=> $poll_id,
			'return'	=> 'true',
			'last'		=> $last,
			'type'		=> isset($_query['inline_message_id']) ? 'inline' : 'private'
			]));
		// \lib\db::rollback();

		if(!$debug_status)
		{
			return ['text' => '❗️' . $debug['messages']['error'][0]['title']];
		}
		return ['text' => \lib\debug::compile()['title']];
	}

	public static function new()
	{
		callback_query::edit_message(\content\saloos_tg\sarshomar_bot\commands\step_create::start());
		\lib\storage::set_disable_edit(true);
		return [];
	}

	public static function edit($_query, $_data_url)
	{
		session::set('poll', $_data_url[2]);
		$return = \content\saloos_tg\sarshomar_bot\commands\step_create::start(null, true);
		session::remove_back('expire', 'inline_cache', 'ask');
		session::remove('expire', 'inline_cache', 'ask');
		callback_query::edit_message($return);
	}

	public static function status($_query, $_data_url)
	{
		$poll = session::get('poll');
		session::remove('poll');
		step::stop();
		\lib\storage::set_disable_edit(true);

		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' => 'array',
			'request' => [
				'status' 	=> $_data_url[2],
				'id' 		=> $_data_url[3]
			]]);
		$request_status = \lib\main::$controller->model()->poll_set_status();

		$debug_status = \lib\debug::$status;
		$debug = \lib\debug::compile();
		\lib\debug::$status = 1;


		if($poll)
		{
			$result = ask::make(null, null, [
				'poll_id' 	=> $_data_url[3],
				'return'	=> true
				]);
			$result['reply_markup'] = menu::main(true);
			$result = \content\saloos_tg\sarshomar_bot\commands\step_create::make_draft($poll, function($_maker)
				{
					unset($_maker->message->message['description']);
				});
			unset($result['reply_markup']);
			callback_query::edit_message($result);
			$main = menu::main()[0];
			$main['method'] = 'sendMessage';
			bot::sendResponse($main);
			bot::sendResponse(ask::make(null, null, [
				'poll_id' 	=> $poll,
				'return'	=> true
				]));
		}
		else
		{
			callback_query::edit_message(ask::make(null, null, [
				'poll_id' 	=> $_data_url[3],
				'return'	=> true
				]));
		}

		if($debug_status !== 1)
		{
			if(isset($debug['messages']['error'][0]))
			{
				return ['text' => '❗️' . $debug['messages']['error'][0]['title']];
			}
				return ['text' => '⚠️' . $debug['messages']['warn'][0]['title']];
		}
		else
		{
			return ['text' => '✅' . $debug['title']];
		}

	}

	public static function report($_query, $_data_url, $_short_link = null)
	{
		$short_link = !is_null($_short_link) ? $_short_link : $_data_url[2];
		$maker = new make_view($short_link);

		if(!language::check())
		{
			language::set($maker->query_result['language']);
		}
		$maker->message->add_title();
		$maker->message->add_poll_list(false, false);
		if(!is_null($_data_url) && count($_data_url) == 4)
		{
			$tag = utility::un_tag($_data_url[3]);
			$tag = utility::tag($tag);
			$maker->message->add('report', '#' . T_('Report') . ' ' . $tag);
			\lib\storage::set_disable_edit(true);
			session::remove('expire', 'inline_cache', 'spam');
			$return = $maker->make();
			\lib\utility\report::set($maker->poll_id, bot::$user_id, "report:$_data_url[3]");
		}
		else
		{
			$maker->message->add('report', '#' . T_('Report'));
			$maker->inline_keyboard->add_report_status();
			$return = $maker->make();
			session::remove_back('expire', 'inline_cache', 'ask');
			session::remove('expire', 'inline_cache', 'ask');
			$return["response_callback"] = utility::response_expire('spam');
		}

		if(!is_null($_query))
		{
			callback_query::edit_message($return);
			return [];
		}
		return $return;
	}
}
?>