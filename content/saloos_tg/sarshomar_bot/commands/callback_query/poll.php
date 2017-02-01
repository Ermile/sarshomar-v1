<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \lib\telegram\step;

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
				'my_poll' 	=> true,
				'from'  	=> (int) $start,
				'to'  		=> (int) ($start + $message_per_page),
			]
			]);
		$search = \lib\main::$controller->model()->poll_search(true);

		$query_result = $search['data'];

		$total_page = ceil($search['total'] / $message_per_page);

		$message = utility::nubmer_language($page . "/" . $total_page) . "\n";
		foreach ($query_result as $key => $value) {
			$message .= $value['title'];
			$message .= utility::nubmer_language("($value[count_vote])");
			$message .= ' - ' . T_(ucfirst($value['status']));
			$message .= "\n";
			$message .= "/sp_" . $value['id'];
			$message .= "\n\n";
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
			$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
		}
		$return['parse_mode'] = "HTML";
		if(is_null($_query))
		{
			return $return;
		}
		callback_query::edit_message($return);
	}

	public static function discard($_query, $_data_url)
	{
		$poll_id = $_data_url[2];
		$return = ['text' => 'Your poll Discarded.'];
		step::stop();
		session::remove('poll');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');

		$edit = \content\saloos_tg\sarshomar_bot\commands\step_create::make_draft($poll_id, function($_maker){
			$_maker->message->message['sucsess'] = T_('Poll Discarded');
			$_maker->message->add("discard", '#'.T_('Discarded'));
		});

		unset($edit['reply_markup']);
		unset($edit['response_callback']);
		callback_query::edit_message($edit);
		return $return;
	}

	public static function save($_query, $_data_url)
	{
		$poll_id = $_data_url[2];
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' =>
			[
			'id' 		=> $poll_id,
			'status'	=> 'publish'
			]
		]);
		$add_poll = \lib\main::$controller->model()->add_poll(['method' => 'patch']);
		if(\lib\debug::$status)
		{
			step::stop();
			$edit = ask::make(null, null, $add_poll['id']);
			session::remove_back('expire', 'inline_cache');
			session::remove('expire', 'inline_cache');
			callback_query::edit_message($edit);
		}
		else
		{
			$_errors = \lib\debug::compile();
			$errors = $_errors['messages']['error'];

			$edit = \content\saloos_tg\sarshomar_bot\commands\step_create::make_draft($poll_id, function($_maker) use ($errors){
					$_maker->message->message['sucsess'] = T_('Error in poll publish');
					$error_text = [];
					foreach ($errors as $key => $value) {
						$error_text[] = "❌ $value[title]";
					}
					$_maker->message->add("insert", join($error_text, "\n"));
					$_maker->message->add("error", '#'.T_('Error'));
			});

			callback_query::edit_message($edit);
			session::remove_back('expire', 'inline_cache', 'create');
			return [];
		}

		return [];
	}

	public static function pause($_query, $_data_url)
	{
		$short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($short_link);
		$poll_result = \lib\db\polls::get_poll($poll_id);
		$status = $poll_result['status'];
		if($status == 'deleted')
		{
			self::delete($_query, $_data_url);
			return ;
		}
		$result = \lib\db\polls::update(['post_status' => 'pause'], $poll_id);
		self::get_after_change($poll_id, $_query);
	}

	public static function publish($_query, $_data_url)
	{
		$short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($short_link);
		$poll_result = \lib\db\polls::get_poll($poll_id);
		$status = $poll_result['status'];
		if($status == 'deleted')
		{
			self::delete($_query, $_data_url);
			return ;
		}
		$result = \lib\db\polls::update(['post_status' => 'publish'], $poll_id);
		self::get_after_change($poll_id, $_query);
	}

	public static function back()
	{
		step::stop();
		session::remove('poll');
	}

	public static function delete($_query, $_data_url)
	{
		$poll_id = isset($_data_url[2]) ? $_data_url[2] : null;
		if(!$poll_id)
		{
			session::remove_back('expire', 'inline_cache', 'create');
			session::remove('expire', 'inline_cache', 'create');
			callback_query::edit_message(['text' => utility::tag(T_("Add poll canceled"))]);
			step::stop();
			return [];
		}
		$delete = \lib\main::$controller->model()->poll_delete(['id' => $poll_id]);
		return [];
		\lib\storage::set_disable_edit(true);
		$maker = new make_view($poll_id);
		$maker->message->add_title(false);
		$maker->message->add_poll_chart(true);
		$maker->message->add_poll_list(true);
		$maker->message->add('deleted', '#' . T_('Deleted'));
		$return = $maker->make();
		callback_query::edit_message($return);
	}

	public static function get_after_change($_poll_id, $_query = false)
	{
		$response = session::get('expire', 'inline_cache', 'ask');
		if(isset($response->result))
		{
			$r_message_id = $response->result->message_id;
			$r_chat_id = $response->result->chat->id;

			$q_message_id = $_query['message']['message_id'];
			$q_chat_id = $_query['message']['chat']['id'];

			if($r_message_id == $q_message_id && $r_chat_id == $q_chat_id)
			{
				session::remove('expire', 'inline_cache', 'ask');
			}
		}

		\lib\storage::set_disable_edit(true);
		$maker = new make_view(bot::$user_id, $_poll_id, true);
		$maker->message->add_title();
		$maker->message->add_poll_chart(true);
		$maker->message->add_poll_list(true);
		$maker->message->add_telegram_link();
		$maker->message->add_telegram_tag();

		if(!$_query)
		{
			$maker->inline_keyboard->add_poll_answers();
		}
		$maker->inline_keyboard->add_guest_option(['skip' => false, 'poll_option' => true]);
		$return = $maker->make();
		if(!$_query)
		{
			$return["response_callback"] = utility::response_expire('ask', [
				'reply_markup' => [
				'inline_keyboard' => [$maker->inline_keyboard->get_guest_option(['skip' => false, 'poll_option' => true])]
				]
				]);
		}
		callback_query::edit_message($return);
	}

	public static function report($_query, $_data_url, $_short_link = null)
	{
		$short_link = !is_null($_short_link) ? $_short_link : $_data_url[2];
		$maker = new make_view(bot::$user_id, $short_link);
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