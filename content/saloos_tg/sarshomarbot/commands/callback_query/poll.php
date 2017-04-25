<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;

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
		$message_per_page = 10;
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
		$search = \lib\main::$controller->model()->poll_search();

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
			$status_emoji = ['publish' => '✳️', 'stop' => '🚫', 'pause' => '⛔️', 'draft' => '📝'];
			$message = utility::nubmer_language($page . "/" . $total_page) . "\n";
			foreach ($query_result as $key => $value) {
				$message .= ' ' .$status_emoji[$value['status']];
				$message .= $value['title'];
				if($value['status'] != 'draft')
				{
					$message .= ' ' . utility::nubmer_language("($value[count_vote])");
				}
				$message .= "\n";
				$message .= "/" . $value['id'];
				$message .= "\n";
			}
		}
		$return = ['text' => $message];
		if($total_page > 1)
		{
			if($page > 2)
			{
				$inline_keyboard[0][] = ["text" => "⏮", "callback_data" => "poll/list/1"];
			}
			if($page > 1)
			{
				$inline_keyboard[0][] = ["text" => "◀️", "callback_data" => "poll/list/" . ($page-1)];
			}



			if($page < $total_page)
			{
				$inline_keyboard[0][] = ["text" => "▶️", "callback_data" => "poll/list/" . ($page+1)];
			}

			if(($page + 1) < $total_page)
			{
				$inline_keyboard[0][] = ["text" => "⏭", "callback_data" => "poll/list/" . $total_page];
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
		$subport = null;
		if(isset($_data_url[3]) && substr($_data_url[3], 0, 1) == ':')
		{
			$subport = \lib\utility\shortURL::decode(substr($_data_url[3], 1));
			$last = null;
		}

		step::stop();
		$poll_id = session::get('answer_descriptive', 'id');
		$text = session::get('answer_descriptive', 'text');
		session::remove('answer_descriptive');

		if($status != 'answer')
		{
			bot::sendResponse([
				'text' 						=> utility::tag(T_('Cancel answering')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
				]);
			return ;
		}
		$request = [
			'id' => $poll_id,
			'descriptive' => $text,
		];

		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' 	=> 'array',
			'request' => [
				'id' 		=> $poll_id,
			]
		]);
		$get_answer = \lib\main::$controller->model()->poll_answer_get([]);
		if(!empty($get_answer['my_answer']))
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => ['id' => $poll_id]]);
			\lib\main::$controller->model()->poll_answer_delete(['id' => $poll_id]);
		}
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $request]);
		$add_poll = \lib\main::$controller->model()->poll_answer_add(['method' => 'post']);

		$debug_status = \lib\debug::$status;
		$debug = \lib\debug::compile();

		\lib\debug::$status = 1;

		if(!$debug_status)
		{
			bot::sendResponse([
				'text' 						=> utility::tag(T_('An error occurred while submitting the answer')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
			]);
			return ['text' => '❗️' . $debug['messages']['error'][0]['title']];
		}
		else
		{
			// session::remove_back('expire', 'inline_cache', 'answer_descriptive');
			// session::remove('expire', 'inline_cache', 'answer_descriptive');
			$maker = new make_view($poll_id);
			$maker->message->add_title();
			$maker->message->message['title'] = '❔ ' . $maker->message->message['title'];
			$maker->message->add('answer' , '📝' . $text);
			$maker->message->add('answer_line' , "");
			$maker->message->add('answer_verify' , '✅ ' . T_("Your answer has been submitted"));
			$maker->message->add('tag' ,  utility::tag(T_("Submit answer")));
			bot::sendResponse([
				'text' 						=> utility::tag(T_('Submit answer')),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
			]);
			if($subport)
			{
				self::subport_update(['subport' => $subport], $poll_id, null, $_query);
			}
			// \lib\db::rollback();
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
		$last = null;
		$subport = null;
		$oprator = null;
		if(count($_data_url) == 4)
		{
			list($class, $method, $poll_id, $answer) = $_data_url;
		}
		elseif (count($_data_url) >= 5) {
			list($class, $method, $poll_id, $answer, $last) = $_data_url;
			if(substr($last, 0, 1) == ':')
			{
				$subport = \lib\utility\shortURL::decode(substr($last, 1));
				$last = null;
			}
			elseif(substr($last, 0, 1) == '+')
			{
				$oprator = substr($last, 1);
				if(isset($_data_url[6]))
				{
					$last = $_data_url[6];
				}
			}
		}
		$maker = new make_view($poll_id);
		$md5_result = md5(json_encode($maker->query_result['result']));
		if($oprator == 'multi')
		{
			$answer = explode("_", $answer);
			$answer = array_fill_keys($answer, true);
			session::remove('expire', 'command', 'multi_answer');
		}
		elseif(isset($maker->query_result['options']['multi']) && is_numeric($answer))
		{
			\lib\storage::set_current_command(true);
			$multi = session::get('expire', 'command', 'multi_answer', $poll_id);
			if(is_null($multi))
			{
				$multi = (object) ['id' => $poll_id, 'answers' => []];
			}
			if(isset($multi->answers))
			{
				$multi->answers = (array) $multi->answers;
			}
			if(in_array($answer, $multi->answers))
			{
				unset($multi->answers[array_search($answer, $multi->answers)]);
				$add = false;
			}
			else
			{
				$multi->answers[] = $answer;
				$add = true;
			}
			session::set('expire', 'command', 'multi_answer', $poll_id, $multi);
			callback_query::edit_message(ask::make(null, null, [
				'poll_id' 	=> $poll_id,
				'return'	=> true,
				'last'		=> $last,
				'type'		=> 'private',
				'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
				]));
			if($add)
			{
				return ['text' => T_("add answer :answer", ['answer' => $answer])];
			}
			else
			{
				return ['text' => T_("remove answer :answer", ['answer' => $answer])];
			}
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
		// $valid_id = [58164083, 46898544];
		// if(!in_array(bot::response('from'), $valid_id))
		// {
		// 	if(isset($get_answer['available']) && empty($get_answer['available']))
		// 	{
		// 		return ['text' => '❗️' . T_("You was already answered to this poll")];
		// 	}
		// }
		$request = ['id' => $poll_id];

		$api_method = 'add';
		$for_delete = session::get('expire', 'command', 'poll_delete');
		if($for_delete && $for_delete->id == $poll_id && $for_delete->answer == $answer)
		{
			$api_method = 'delete';
			if($answer == 'like')
			{
				$answer = 'dislike';
			}
		}
		elseif(isset($get_answer['my_answer'][0]) &&
			$get_answer['my_answer'][0]['key'] == $answer &&
			in_array('delete', $get_answer['available'])
			)
		{
			$api_method = 'warn_delete';
		}

		switch ($answer) {
			case 'like':
				$request['like'] = true;
				if(isset($get_answer['my_answer'][0]) && in_array('delete', $get_answer['available']))
				{
					$api_method = 'warn_delete';
				}
				break;
			case 'dislike':
				$api_method = 'delete';
				break;
			case 'skip':
				$request['skip'] = true;
				break;
			default:
				$request['answer'] = is_array($answer) ? $answer : [$answer => true];
				break;
		}

		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $request]);

		if($api_method == 'add')
		{
			$add_poll = \lib\main::$controller->model()->poll_answer_add([
				'method' => in_array('edit', $get_answer['available']) ? 'put' : 'post'
				]);
		}
		elseif($api_method == 'delete')
		{
			$add_poll = \lib\main::$controller->model()->poll_answer_delete(['id' => $poll_id]);
		}
		$debug_status = \lib\debug::$status;
		$debug = \lib\debug::compile();
		if ($api_method == 'warn_delete') {
			$debug_status = 2;
			$debug['messages']['warn'][0]['title'] = T_("If you intend to delete your vote, tap once more");
			session::set('expire', 'command', 'poll_delete', ['id' => $poll_id, 'answer' => $answer]);
			\lib\storage::set_current_command(true);
		}

		\lib\debug::$status = 1;

		if(!$debug_status)
		{
			callback_query::answer_message(['text' => '❗️' . $debug['messages']['error'][0]['title'], "show_alert" => true]);
		}
		elseif($debug_status == 2)
		{
			callback_query::answer_message(['text' => '⚠️' . $debug['messages']['warn'][0]['title'], "show_alert" => true]);
		}
		$title = !isset($debug['messages']['true']) ? $debug['title'] : $debug['messages']['true'][0]['title'];
		callback_query::answer_message(['text' => '✅ ' . $title, "show_alert" => true]);

		if(isset($_query['inline_message_id']) || $subport)
		{
			if(isset($_query['inline_message_id']))
			{
				self::subport_update(['inline_message_id' => $_query['inline_message_id']], $poll_id, $md5_result, $_query);
			}
			else
			{
				self::subport_update(['subport' => $subport], $poll_id, $md5_result, $_query);
				\lib\storage::set_disable_edit(false);
			}
		}
		else
		{
			callback_query::edit_message(ask::make(null, null, [
				'poll_id' 	=> $answer == 'skip' ? null : $poll_id,
				'return'	=> true,
				'last'		=> $last,
				'type'		=> 'private',
				'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
				]));
		}
		// \lib\db::rollback();

	}

	public static function new()
	{
		bot::sendResponse(\content\saloos_tg\sarshomarbot\commands\step_create::start());
		return [];
	}

	public static function edit($_query, $_data_url)
	{
		session::set('poll', $_data_url[2]);
		$return = \content\saloos_tg\sarshomarbot\commands\step_create::start(null, true);
		session::remove_back('expire', 'inline_cache', 'ask');
		session::remove('expire', 'inline_cache', 'ask');
		callback_query::edit_message($return);
	}

	public static function status($_query, $_data_url)
	{
		$poll = session::get('poll');
		session::remove('poll');
		step::stop();

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

			$main = menu::main()[0];
			$main['method'] = 'sendMessage';
			bot::sendResponse($main);
			bot::sendResponse(ask::make(null, null, [
				'poll_id' 	=> $poll,
				'return'	=> true,
				]));
		}
		else
		{
			\lib\storage::set_disable_edit(true);
			callback_query::edit_message(ask::make(null, null, [
				'poll_id' 	=> $_data_url[3],
				'return'	=> true,
				'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
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
			\lib\storage::set_disable_edit(true);
			$maker->message->add('report', '#' . T_('Report'));
			$maker->inline_keyboard->add_report_status();
			$maker->inline_keyboard->add([[
				"text" => T_("Back"),
				'callback_data' => 'ask/update/'.$short_link .'/update'
				]]);
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

	public static function answer_results($_query, $_data_url, $_id = null)
	{
		\lib\storage::set_disable_edit(true);
		$message_per_page = 8;
		if(is_null($_query) || !isset($_data_url[3]))
		{
			$page = 1;
			$start = 0;
		}
		else
		{
			$page = (int) $_data_url[3];
			$start = (($page-1) * $message_per_page);
		}

		if(isset($_id))
		{
			$id = $_id;
		}
		else
		{
			$id = $_data_url[2];
		}


		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' => 'array',
			'request' => [
				'id' 	=> $id,
				'from'  	=> (int) $start,
				'to'  		=> (int) ($start + $message_per_page),
			]]);
		$answers_list = \lib\main::$controller->model()->get_poll_answers_list();

		$query_result = $answers_list['data'];

		$total_page = ceil($answers_list['total'] / $message_per_page);


		$message = utility::nubmer_language($page . "/" . $total_page) . ' /'.$_data_url[2] . "\n";
		foreach ($query_result as $key => $value) {
			if(isset($value['profile']))
			{
				$display_name = "<strong>" . $value['profile']['displayname'] ."</strong>";
				if(isset($value['profile']['telegram_id']))
				{

					$telegram = \lib\db\options::get([
						"option_cat" => "telegram",
						"option_key" => "id",
						"option_value" => $value['profile']['telegram_id'],
						"limit" => 1
						]);
					if(!empty($telegram) && isset($telegram['meta']['username']))
					{
						$get_user = json_decode(\lib\db\users::get($telegram['user_id'])['user_meta'], true);
						if(isset($get_user['username']))
						{
							$display_name = "@".$get_user['username'];
						}
						else
						{
							$display_name = "@".$telegram['meta']['username'];
						}
					}
				}
				$message .= $display_name;
				switch ($value['type']) {
					case 'descriptive':
						$message .= ":\n" . $value['text'] . "\n\n";
						break;
					case 'select':
						$message .= ": " . utility::nubmer_language($value['key']) . "\n";
						break;

					default:
						$message .= "\n";
						break;
				}
			}
			else
			{
				switch ($value['type']) {
						case 'descriptive':
							$message .= $value['text'] . "\n\n";
							break;
					}
			}
		}
		$return = ['text' => $message];
		$inline_keyboard[] = [['text' => T_("Back"), 'callback_data' => 'ask/update/'. $id  . '/update']];

		if($total_page > 1)
		{
			if($page > 2)
			{
				$inline_keyboard[1][] = ["text" => "⏮", "callback_data" => "poll/answer_results/". $id ."/1"];
			}
			if($page > 1)
			{
				$inline_keyboard[1][] = ["text" => "◀️", "callback_data" => "poll/answer_results/". $id ."/" . ($page-1)];
			}



			if($page < $total_page)
			{
				$inline_keyboard[1][] = ["text" => "▶️", "callback_data" => "poll/answer_results/". $id ."/" . ($page+1)];
			}

			if(($page + 1) < $total_page)
			{
				$inline_keyboard[1][] = ["text" => "⏭", "callback_data" => "poll/answer_results/". $id ."/" . $total_page];
			}
		}
		if(isset($inline_keyboard))
		{
			$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
		}
		$return['parse_mode'] = "HTML";
		$return["response_callback"] = utility::response_expire('ask');
		if(isset($_query))
		{
			callback_query::edit_message($return);
		}
		else
		{
			return $return;
		}
	}

	public static function subport_update($_options, $_poll_id, $_md5_result = false, $_query)
	{
		if(isset($_options['subport']))
		{
			$get_subport = \lib\db\options::get([
				'id'		=> $_options['subport'],
				'limit'		=> 1
				]);
			$inline_message_id = $get_subport['meta'];
		}
		else
		{
			$inline_message_id = $_options['inline_message_id'];
			$get_subport = false;
		}
		if(isset($_query['inline_message_id']))
		{
			$get_flag = \lib\db\options::get([
				"option_cat" => "telegram",
				"option_key" => "subport_flag",
				"option_meta" => $inline_message_id,
				"limit" => 1
			]);
			if(!empty($get_flag))
			{
				$flag = ['gift'];
			}
			else
			{
				$flag = [];
			}
		}
		else
		{
			$flag = [];
		}

		$get_inline_lock = \lib\db\options::get([
			"option_cat" => "telegram",
			"option_key" => "inline_message_lock",
			"option_value" => $inline_message_id,
			"limit" => 1
		]);
		// $valid_id = [58164083, 46898544];
		// if(in_array(bot::response('from'), $valid_id))
		// {
		// 	$edit = ask::make(null, null, [
		// 		'poll_id' 		=> $_poll_id,
		// 		'return'		=> true,
		// 		'type'			=> 'inline',
		// 		'md5_result'	=> $_md5_result,
		// 		'inline_id'		=> $get_subport ? $get_subport['value'] : null,
		// 		'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
		// 		'flag'	=>  $flag,
		// 	]);
		// }
		if(empty($get_inline_lock) || !$get_inline_lock)
		{
			\lib\db\options::insert([
				"option_cat" => "telegram",
				"option_key" => "inline_message_lock",
				"option_value" => $inline_message_id,
				"option_meta"	=> time(),
				// "option_status" => 'disable',
			]);
			$get_inline_lock = \lib\db::query("SELECT LAST_INSERT_ID() as id");
			$get_inline_lock_id = $get_inline_lock->fetch_object()->id;
			$edit = ask::make(null, null, [
				'poll_id' 		=> $_poll_id,
				'return'		=> true,
				'type'			=> 'inline',
				'md5_result'	=> $_md5_result,
				'inline_id'		=> $get_subport ? $get_subport['value'] : null,
				'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
				'flag'	=>  $flag,
			]);
			$edit['inline_message_id'] = $inline_message_id;
			if($edit)
			{
				callback_query::edit_message($edit);
			}
			// \lib\db\options::update([
			// 	"option_status" => 'enable'
			// ], $get_inline_lock_id);
		}
		// elseif($get_inline_lock['status'] == 'disable')
		// {
		// 	\lib\db\options::update([
		// 		"option_meta" => ++$get_inline_lock['meta']
		// 	], $get_inline_lock['id']);
		// }
		elseif($get_inline_lock)
		{
			if((int) $get_inline_lock['meta'] + 3 > time())
			{
				return;
			}
			\lib\db\options::update([
				"option_meta" => time()
			], $get_inline_lock['id']);
			$edit = ask::make(null, null, [
				'poll_id' 	=> $_poll_id,
				'return'	=> true,
				'type'		=> 'inline',
				'md5_result'	=> $_md5_result,
				'inline_id'	=> $get_subport ? $get_subport['value'] : null,
				'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
				'flag'	=>  $flag,
			]);

			if($edit)
			{
				$edit['inline_message_id'] = $inline_message_id;
				$in_edit = \lib\db\options::get([
					"option_cat" => "telegram",
					"option_key" => "inline_message_in_edit",
					"option_value" => $inline_message_id,
					"limit" => 1
				]);
				if(isset($in_edit['status']))
				{
					if($in_edit['status'] == 'enable')
					{
						\lib\db\options::update([
							"option_status" => 'disable'
						], $in_edit['id']);

						callback_query::edit_message($edit);

						\lib\db\options::update([
							"option_status" => 'enable'
						], $in_edit['id']);
					}
					else
					{
						return;
					}
				}
				else
				{
					\lib\db\options::insert([
						"option_cat" => "telegram",
						"option_key" => "inline_message_in_edit",
						"option_value" => $inline_message_id,
					]);
					callback_query::edit_message($edit);
				}

			}
			// \lib\db\options::update([
			// 	"option_status" => 'enable'
			// ], $get_inline_lock['id']);
			// $get_inline_lock = \lib\db\options::get([
			// 	"id" => $get_inline_lock['id'],
			// 	"limit" => 1
			// ]);
			// if((int) $get_inline_lock['meta'] > 2)
			// {
			// 	$edit = ask::make(null, null, [
			// 		'poll_id' 		=> $_poll_id,
			// 		'return'		=> true,
			// 		'type'			=> 'inline',
			// 		'md5_result'	=> $_md5_result,
			// 		'inline_id'		=> $get_subport ? $get_subport['value'] : null,
			// 		'text_type'	=>  isset($_query['message']['caption']) ? 'caption' : 'text',
			// 	'flag'	=>  $flag,
			// 	]);
			// 	$edit['inline_message_id'] = $inline_message_id;
			// 	if($edit)
			// 	{
			// 		callback_query::edit_message($edit);
			// 	}
			// }
		}
	}
}
?>