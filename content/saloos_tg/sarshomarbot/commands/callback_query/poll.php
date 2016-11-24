<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;

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
		$count = \lib\db\polls::search(null, ['user_id'=> bot::$user_id, 'get_count' => true, 'pagenation' => false]);
		$message_per_page = 5;
		$total_page = ceil($count / $message_per_page);
		if(is_null($_query))
		{
			$start = 0;
			$end = $message_per_page;
			$page = 1;
		}
		$query_result = \lib\db\polls::search(null, [
			'user_id'=> bot::$user_id,
			'pagenation' => false,
			'start_limit' => $start,
			'end_limit' => $end,
			'my_poll' => true
			]);
		$message = $page . "/" . $total_page . "\n";
		foreach ($query_result as $key => $value) {
			$message .= htmlentities($value['title']);
			$message .= " ($value[total])";
			$message .= "\n";
			$short_link = \lib\utility\shortURL::encode($value['id']);
			$message .= "/sp\_$short_link";
			$message .= "\n\n";
		}

		if(is_null($_query))
		{
			return ['text' => $message];
		}
		// handle::send_log($query_result);
	}

	public static function discard($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);

		$poll_id = $_data_url[2];
		$poll_draft = session::get('poll', $poll_id);
		$poll_title = $poll_draft->title;
		$poll_answers = $poll_draft->answers;
		$poll = ['title' => $poll_title];
		foreach ($poll_answers as $key => $value) {
			$poll['meta']['opt'][] = ["txt" => $value];
		}
		$maker = new make_view(bot::$user_id, $poll);
		$maker->message->add_title(false);
		$maker->message->add_poll_list(null, false);
		$maker->message->add('cancel', '#Cancel');
		callback_query::edit_message(["text" => $maker->message->make()]);
		session::remove('poll', $poll_id);
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
			'post_type'		=> 'select'
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
		$result = \lib\db\polls::update(['post_status' => 'pause'], $poll_id);
		self::get_after_change($poll_id, $_query);
	}

	public static function publish($_query, $_data_url)
	{
		$short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($short_link);
		$result = \lib\db\polls::update(['post_status' => 'publish'], $poll_id);
		self::get_after_change($poll_id, $_query);
	}

	public static function delete($_query, $_data_url)
	{
		$short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($short_link);
		$result = \lib\db\polls::update(['post_status' => 'deleted'], $poll_id);
		\lib\storage::set_disable_edit(true);
		$maker = new make_view(bot::$user_id, $poll_id, true);
		$maker->message->add_title(false);
		$maker->message->add_poll_chart(true);
		$maker->message->add_poll_list(true);
		$maker->message->add('#' . T_('Deleted'));
		$return = $maker->make();
		callback_query::edit_message($return);
	}

	public static function get_after_change($_poll_id, $_query = false)
	{
		$response = session::get('expire', 'inline_cache', 'ask');
		$r_message_id = $response->result->message_id;
		$r_chat_id = $response->result->chat->id;

		$q_message_id = $_query['message']['message_id'];
		$q_chat_id = $_query['message']['chat']['id'];

		if($r_message_id == $q_message_id && $r_chat_id == $q_chat_id)
		{
			session::remove('expire', 'inline_cache', 'ask');
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
}
?>