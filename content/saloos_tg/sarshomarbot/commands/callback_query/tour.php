<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class tour
{

	public static function start($_query, $_data_url)
	{
		if(isset($_data_url[1]) && $_data_url[1] == 'end')
		{
			return \content\saloos_tg\sarshomarbot\commands\step_tour::step1(true);
		}
	}
}
?>