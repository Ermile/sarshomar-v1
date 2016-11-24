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
		$result['switch_pm_text'] = "New Poll";
		$result['switch_pm_parameter'] = "create";

		$search = \lib\utility\safe::safe($inline_query['query']);
		if($search == '')
		{
			$search = "_";
		}
		if(preg_match("/^\s*sp_(.*)$/", $search, $link_id))
		{
			$id = \lib\utility\shortURL::decode($link_id[1]);
			$query_result = \lib\db\polls::get_poll($id);
			$query_result = $query_result ? [$query_result] : [];
		}
		else
		{
			$query_result = \lib\db\polls::search($search);
		}

		$result['results'] = [];
		$step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
		foreach ($query_result as $key => $value) {
			$row_result = [];
			$row_result['type'] = 'article';
			$row_result['id'] = $value['id'];

			$maker = new make_view(bot::$user_id, $value);

			$maker->message->add_title();
			$maker->message->add_poll_chart();
			$maker->message->add_poll_list();
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$row_result['title'] = html_entity_decode($value['title']);


			$row_result['url'] = 'https://sarshomar.com/sp_' . $maker->short_link;

			if(array_key_exists('description', $row_result) && !is_null($row_result['description']))
			{
				$row_result['description'] = $value['contnet'];
			}
			$row_result['hide_url'] = false;

			$maker->inline_keyboard->add_poll_answers();
			$maker->inline_keyboard->add_guest_option(['skip' => false, 'update' => false, 'report' => true]);

			$inline_keyboard = $maker->inline_keyboard->make();

			if(!empty($inline_keyboard)) {
				$row_result['reply_markup']['inline_keyboard'] = $inline_keyboard;
			}

			$row_result['input_message_content'] = [
				'message_text' 				=> $maker->message->make(),
				'parse_mode' 				=> 'Markdown',
				'disable_web_page_preview' 	=> true
			];
			$result['results'][] = $row_result;
		}
		session::remove_back('expire', 'inline_cache');
		return $result;
	}
}
?>