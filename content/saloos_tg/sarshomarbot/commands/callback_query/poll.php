<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;
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
		$count = (int) \lib\db\polls::search(null, [
			'get_count' => true,
			'user_id'=> bot::$user_id,
			'pagenation' => false,
			'my_poll' => true,
			'post_status' => ['in', "('publish', 'pause', 'draft')"],
			]);
		$message_per_page = 5;
		$total_page = ceil($count / $message_per_page);

		$page = (int) $_data_url[2];
		$start = ($page - 1) * $message_per_page;
		$end = $message_per_page;
		if(is_null($_query) || $page > $total_page)
		{
			$start = 0;
			$page = 1;
		}
		$query_result = \lib\db\polls::search(null, [
			'user_id'=> bot::$user_id,
			'pagenation' => false,
			'start_limit' => $start,
			'end_limit' => $end,
			'my_poll' => true,
			'post_status' => ['in', "('publish', 'pause', 'draft')"],
			'order' => 'DESC'
			]);
		$message = $page . "/" . $total_page . "\n";
		foreach ($query_result as $key => $value) {
			$message .= $value['title'];
			$message .= " ($value[total])";
			$message .= ' - ' . T_(ucfirst($value['status']));
			$message .= "\n";
			$short_link = \lib\utility\shortURL::encode($value['id']);
			$message .= "/sp\_$short_link";
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

			if(($page + 2) < $total_page)
			{
				$inline_keyboard[0][] = ["text" => T_("Last"), "callback_data" => "poll/list/" . $total_page];
			}
			$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
		}
		if(is_null($_query))
		{
			return $return;
		}
		callback_query::edit_message($return);
	}

	public static function discard($_query, $_data_url)
	{
		step::stop();
		$edit = \content\saloos_tg\sarshomarbot\commands\step_create::make_draft(function($_maker){
			$_maker->message->message['sucsess'] = T_('Poll Discarted');
		});

		session::remove('poll');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		unset($edit['reply_markup']);
		unset($edit['response_callback']);
		callback_query::edit_message($edit);
		return ['text' => 'Your poll Discarted.'];
	}

	public static function save($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);

		$poll_id = $_data_url[2];
		$poll_draft = session::get('poll', $poll_id);
		$poll_title = $poll_draft->title;

		$poll_answers = (array) $poll_draft->answers;

		$answers = [];
		foreach ($poll_answers as $key => $value) {
			$answers[]['txt'] = $value;
		}

		\lib\db::transaction();

		$poll_id = \lib\db\polls::insert([
			'user_id' 		=> bot::$user_id,
			'post_title'	=> $poll_title,
			'post_status' 	=> 'publish',
			'post_type'		=> 'select',
			'post_privacy'	=> 'private',
			'post_language'	=> language::check(true)
			]);
		\lib\utility\answers::insert(['poll_id' => $poll_id, 'answers' => $answers]);
		if(\lib\debug::$status)
		{
			\lib\db::commit();
		}
		if($poll_id)
		{
			self::get_after_change($poll_id, false);
		}
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

	public static function delete($_query, $_data_url)
	{
		$short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($short_link);
		$poll_result = \lib\db\polls::get_poll($poll_id);
		$status = $poll_result['status'];
		$status = ($status == 'draft') ? 'pause' : $status;
		if($status != 'pause' && $status != 'deleted')
		{
			self::publish($_query, $_data_url);
			return ;
		}
		$result = \lib\db\polls::update(['post_status' => 'deleted'], $poll_id);
		\lib\storage::set_disable_edit(true);
		$maker = new make_view(bot::$user_id, $poll_id, true);
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
		$maker->message->add_title();
		$maker->message->add_poll_list(false, false);
		if(!is_null($_data_url) && count($_data_url) == 4)
		{
			$maker->message->add('report', '#' . T_('Report') . ' #' . T_(ucfirst($_data_url[3])));
			\lib\storage::set_disable_edit(true);
			session::remove('expire', 'inline_cache', 'spam');
			$return = $maker->make();
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