<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;

class step_answer
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_poll_id = null)
	{
		step::start('answer');
		step::plus(1);
		session::set("step_answer", $_poll_id);
		return [];
	}
	public static function step1($_string){
		// $poll_id = session::get("step_answer");
		// $poll_result = \lib\utility\stat_polls::get_telegram_result($poll_id);
		// $result = $poll_result->get_result('result');
		// step::stop();
		return ['text' => $_string];
	}
}
?>
