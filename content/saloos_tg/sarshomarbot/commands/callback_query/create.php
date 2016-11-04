<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\step_sarshomar;
use \content\saloos_tg\sarshomarbot\commands\handle;
use content\saloos_tg\sarshomarbot\commands\chart;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class create
{
	public static function start($_query, $_data_url)
	{
		$for_edit = session::get_back('expire', 'inline_cache', 'create');
		$text = "شما از ثبت یک سوال انصرافت دادید.\n";
		$text .= "امیدواریم در آینده سوالات زیبا و کاربردی تهیه نمایید.";
		$text .= "\n#create\_cancel";
		$for_edit->result->original_text = $text;
		session::remove('expire', 'inline_cache', 'create');
		step::stop();
		return [];
	}
}
?>