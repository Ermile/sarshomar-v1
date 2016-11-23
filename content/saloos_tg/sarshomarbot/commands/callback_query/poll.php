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
			return self::$method($_query, $_data_url);
		}
		return [];
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
		$poll_tmp = new make_view(bot::$user_id, $poll);
		handle::send_log($poll_tmp);
		// array_pop($poll_tmp['message']);
		// array_pop($poll_tmp['message']);
		// $txt_text = poll_result::get_message($poll_tmp['message']);
		// $txt_text .= "\nCanceled";
		// callback_query::edit_message(["text" => $txt_text]);
		// session::remove('poll', $poll_id);
	}

	public static function publish($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);

		$poll_id = $_data_url[2];
		$poll_draft = session::get('poll', $poll_id);
		$poll_title = $poll_draft->title;
		$poll_answers = (array) $poll_draft->answers;

		$poll_id = \lib\db\polls::insert_quick([
			'user_id' => bot::$user_id,
			'title'=> $poll_title,
			'answers' => $poll_answers
		]);
		if($poll_id)
		{
			$short_link = \lib\utility\shortURL::encode($poll_id);
			$poll_result = ask::get_poll_result($short_link, $poll_id, 0);
			callback_query::edit_message($poll_result);
		}
	}
}
?>