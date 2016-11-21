<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\step_sarshomar;
use \content\saloos_tg\sarshomarbot\commands\handle;
use content\saloos_tg\sarshomarbot\commands\chart;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\utility;

class ask
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

	public static function type($_query, $_data_url)
	{
		$type = $_data_url[2];
		session::remove_back('expire', 'inline_cache', 'sarshomar');
		session::remove('expire', 'inline_cache', 'sarshomar');
		callback_query::edit_message(self::make($_query, $_data_url, true));
		return [];
	}

	public static function make($_query, $_data_url, $_return = false)
	{
		$poll = step_sarshomar::step1();
		if($_return)
		{
			return $poll;
		}
		bot::sendResponse($poll);
		return [];
	}

	public static function poll($_query, $_data_url)
	{
		handle::send_log("f");
		$poll_short_link = $_data_url[2];
		$answer_id = $_data_url[3];
		$poll_id = \lib\utility\shortURL::decode($poll_short_link);
		\lib\utility\answers::save(bot::$user_id, $poll_id, $answer_id);
		if(!array_search('message', $_query))
		{
			$poll_result = poll_result::make($value);
			$poll_with_chart = callback_query\ask::get_poll_result($poll_short_link);
			$message = $poll_with_chart['text'];
			$inline_keyboard = $poll_result['inline_keyboard'];

			if(!empty($inline_keyboard)) {
				$reply_markup = [['inline_keyboard'] => $inline_keyboard];
			}
			callback_query::edit_message(['text' => '$message', 'reply_markup' => $reply_markup]);
		}
		else
		{
			$on_edit = session::get_back('expire', 'inline_cache', 'ask', 'on_expire');

			$edit_message = self::get_poll_result($poll_short_link, $poll_id, $answer_id);

			$on_edit->text 				= $edit_message['text'];
			$on_edit->response_callback	= utility::response_expire('ask', ["reply_markup"=>$edit_message['reply_markup']]);
			array_unshift(
				$edit_message['reply_markup']['inline_keyboard'][0],
				utility::inline(T_("Next poll"), "ask/make")
			);
			$on_edit->reply_markup 		= $edit_message['reply_markup'];
		}
		return ["text" => "✅ save your poll"];
	}

	public static function update($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		$ask_expire = session::get('expire', 'inline_cache', 'ask', 'on_expire');
		$message = self::get_poll_result($_data_url[2]);

		if($ask_expire->message_id == $_query['message']['message_id'] AND
			$ask_expire->chat_id == $_query['message']['chat']['id'])
		{
			session::remove('expire', 'inline_cache', 'ask');
		}
		$message['text'] .= "\n Last update: " . date("H:i:s");
 		callback_query::edit_message($message);
		return [];
	}
	public static function get_poll_result($_poll_short_link, $_poll_id = null, $_answer_id = null)
	{
		if(is_null($_poll_id))
		{
			$_poll_id = \lib\utility\shortURL::decode($_poll_short_link);
		}
		$poll_result = \lib\utility\stat_polls::get_telegram_result($_poll_id);
		if(!$poll_result)
		{
			$poll_result = \lib\db\polls::get_poll($_poll_id);
			foreach ($poll_result['meta']['opt'] as $key => $value) {
				$poll_result['result'][$value['txt']] = 0;
			}
		}
		$poll_answer = array();
		$poll_list = '';
		$count = 0;
		$row      = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
		foreach ($poll_result['result'] as $key => $value) {
			$count++;
			$poll_answer[$count] = $value;
			if($_answer_id == $count)
			{
				$poll_list .= '✅ ' . $key . "\n";
			}
			else
			{
				$poll_list .= $row[$count] . ' ' . $key . "\n";
			}
		}
		$url = preg_split("[\/]", $poll_result['url']);
		$text = '['.$poll_result['title'].']' . "(https://sarshomar.com/$/".$url[1].")";
		$text .= "\n";
		$text .= utility::calc_vertical($poll_answer);
		$text .= "\n";
		$text .= $poll_list;
		$text .= '['.T_('Answer link').']' . "(https://telegram.me/SarshomarBot?start=sp_$_poll_short_link)";
		$text .= "\n";
		$text .= "#sarshomar";

		$return = [];
		$inline_keyboard = [[utility::inline(T_("Update result"), "ask/update/" .$_poll_short_link)]];
		if(\lib\db\polls::is_my_poll($_poll_id, bot::$user_id)){
			array_push($inline_keyboard, [utility::inline(T_("Close poll "), "ask/close/" .$_poll_short_link)]);
		}
		$return = [
			'text' 						=> $text,
			'parse_mode' 				=> 'Markdown',
			'disable_web_page_preview' 	=> true,
			'reply_markup' 		=> ["inline_keyboard" => $inline_keyboard]
			];
		return $return;
	}
}
?>