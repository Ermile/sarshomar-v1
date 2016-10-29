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
		$type = $_data_url[1];
		bot::sendResponse(step_sarshomar::step1());
		return ["text" => "hi hey"];
	}
}
?>