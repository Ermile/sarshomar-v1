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

	public static function delete($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		callback_query::edit_message(["text" => 'cancel']);
	}
}
?>