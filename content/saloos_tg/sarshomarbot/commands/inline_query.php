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

		$result_id = md5(microtime(true) . $id);

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
			$query_result = $query_result ? [$query_result] : [];
		}
		else
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'search' 	=> $search,
					'in' 		=> 'me sarshomar',
					'from'		=> !empty($_query['offset']) ? $_query['offset'] : 0
				]
				]);
			$query_result = \lib\main::$controller->model()->poll_search(true);
			if($query_result['from'] < $query_result['total'])
			{
				$result['next_offset'] = $query_result['to'];
			}
			$query_result = $query_result['data'];
		}


		$result['results'] = [];
		$step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
		foreach ($query_result as $key => $value) {
			if(isset($value['options']['multi']))
			{
				continue;
			}
			\lib\define::set_language($value['language'], true);
			$row_result = [];
			$row_result['type'] = 'article';
			if($value['sarshomar'] == true)
			{
				$row_result['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
			}
			else
			{
				$row_result['thumb_url'] = 'http://sarshomar.com/static/images/telegram/sarshomar/sp-users.png';
			}
			$row_result['description'] = '';
			$poll = callback_query\ask::make(null, null, [
				'poll_id' 	=> $value['id'],
				'return'	=> true,
				'type'		=> 'inline',
				'inline_id'	=> $result_id,
				'fn'		=> function($_maker) use(&$row_result)
				{
					$row_result['description'] = '👥 ' . utility::nubmer_language($_maker->query_result['result']['summary']['total']) .' ';
				}
				]);
			$short_dec = preg_replace("/\n/", " ", $value['description']);
			$short_dec = mb_substr($short_dec, 0, 120);

			$row_result['description'] .= $short_dec;

			$row_result['title'] = $value['title'];

			if($value['sarshomar'])
			{
				$row_result['url'] = $value['short_url'];
			}
			$row_result['id'] = $result_id . ':' . $value['id'];

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