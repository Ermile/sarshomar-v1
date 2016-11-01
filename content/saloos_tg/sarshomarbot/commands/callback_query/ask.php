<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\step_sarshomar;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class ask
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 2)
		{
			$method = $_data_url[1];
			return self::$method($_query, $_data_url);
		}
		$type = $_data_url[1];
		session::remove_back('expire', 'inline_cache', 'sarshomar');
		session::remove('expire', 'inline_cache', 'sarshomar');
		callback_query::edit_message(step_sarshomar::step1());
		return ["text" => $type];
	}

	public static function poll($_query, $_data_url)
	{
		//✅
		$poll_short_link = $_data_url[2];
		$poll_id = \lib\utility\shortURL::decode($poll_short_link);

		$poll_answer_id = $_data_url[3];
		\lib\db\answers::save(bot::$user_id, $poll_id, $poll_answer_id);
		$return = [];
		$return["text"] = "✅ save your poll";
		if(count($_data_url) > 4 && $_data_url[4] == 'last')
		{
			$return["response_callback"] = function($_response)
			{
				if($_response->ok)
				{
					bot::sendResponse(step_sarshomar::step1());
				}
			};
		}
		return $return;
	}
}
?>