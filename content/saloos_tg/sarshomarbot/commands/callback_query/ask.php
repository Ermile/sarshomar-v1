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
		if(count($_data_url) > 2)
		{
			$method = $_data_url[1];
			return self::$method($_query, $_data_url);
		}
		$type = $_data_url[1];
		session::remove_back('expire', 'inline_cache', 'sarshomar');
		session::remove('expire', 'inline_cache', 'sarshomar');
		callback_query::edit_message(self::make($_query, $_data_url, true));
		return ["text" => $type];
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
					session::remove('expire', 'inline_cache', 'ask');
				}
			};
		}
		$poll_result = \lib\db\stat_polls::get_telegram_result($poll_id);
		$poll_answer = array();
		$poll_list = '';
		$count = 0;
		$row      = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
		foreach ($poll_result['result'] as $key => $value) {
			$count++;
			$poll_answer[$count] = $value;
			if($poll_answer_id == $count)
			{
				$poll_list .= '✅ ' . $key . "\n";
			}
			else
			{
				$poll_list .= $row[$count] . ' ' . $key . "\n";
			}
		}
		$for_edit = session::get_back('expire', 'inline_cache', 'ask');
		$result = '📊' . $poll_result['title'];
		$result .= "\n";
		$result .= chart::calc_vertical($poll_answer);
		$result .= "\n";
		$result .= $poll_list;
		$url = preg_split("[\/]", $poll_result['url']);
		$result .= "[".$poll_result['title']."](https://telegram.me/SarshomarBot/sp_".$url[1].")";
		$result .= "\n";
		$result .= "[view result](https://sarshomar.com/$/".$url[1].")";
		$result .= "\n";
		$result .= "#sarshomar";
		$for_edit->result->original_text = $result;
		$for_edit->before_edit = self::after_poll($poll_short_link);

		return $return;
	}

	public static function after_poll($_poll_short_link)
	{
		return [function(&$_return, $_options){
			$_return["reply_markup"]['inline_keyboard'] =  [[
				utility::inline(T_("Next poll"), "ask/make/sarshomar"),
				utility::inline(T_("Update result"), "ask/update/" .$_options[0])
			]];
		}, $_poll_short_link];
	}
}
?>