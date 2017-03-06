<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\telegram\tg as bot;

class inline_query
{

	public static function start($_query = null)
	{
		$inline_query = $_query;
		$id = $inline_query['id'];
		$result = ['method' => 'answerInlineQuery'];
		$result['inline_query_id'] = $id;
		$result['is_personal'] = true;
		$result['cache_time'] = 1;
		$result['switch_pm_text'] = T_("Create new poll");
		$result['switch_pm_parameter'] = "new";

		$search = \lib\utility\safe::safe($inline_query['query']);
		$check_language = false;
		if(preg_match("/^\s*\\$(.*)$/", $search, $link_id))
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'id' 		=> $link_id[1],
				]
				]);
			$query_result = \lib\main::$controller->model()->poll_get();
			handle::send_log($query_result);
			$query_result = $query_result ? [$query_result] : [];
		}
		else
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'search' 	=> $search,
					'in' 		=> 'me sarshomar'
				]
				]);
			$query_result = \lib\main::$controller->model()->poll_search(true);
			$query_result = $query_result['data'];
		}


		$result['results'] = [];
		$step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
		foreach ($query_result as $key => $value) {
			\lib\define::set_language($value['language'], true);
			$row_result = [];
			$row_result['type'] = 'article';
			if($value['sarshomar'] == '1')
			{
				$row_result['thumb_url'] = 'http://sarshomar.com/static/images/telegram/sarshomar/sp_sarshomar.png';
			}
			else
			{
				$row_result['thumb_url'] = 'http://sarshomar.com/static/images/telegram/sarshomar/sp-users.png';
			}
			$poll = callback_query\ask::make(null, null, [
				'poll_id' 	=> $value['id'],
				'return'	=> 'true',
				'type'		=> 'inline',
				]);

			$row_result['title'] = html_entity_decode($value['title']);


			$row_result['url'] = $value['short_url'];
			$row_result['id'] = $value['id'];

			$row_result['hide_url'] = false;


			$row_result['reply_markup'] = $poll['reply_markup'];

			$row_result['input_message_content'] = [
				'message_text' 				=> $poll['text'],
				'parse_mode' 				=> $poll['parse_mode'],
				'disable_web_page_preview' 	=> $poll['disable_web_page_preview']
			];
			$result['results'][] = $row_result;
		}
		\lib\define::set_language(callback_query\language::check(true), true);
		session::remove_back('expire', 'inline_cache');

		return $result;
	}
}
?>