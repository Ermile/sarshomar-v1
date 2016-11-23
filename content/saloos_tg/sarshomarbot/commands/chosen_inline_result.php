<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class chosen_inline_result
{

	public static function start($_query = null)
	{
		return [];
		// $user_id = bot::response('from');
		// $inline_message_id = $_query['inline_message_id'];
		// $save = \lib\db\options::insert([
		// 	"user_id" 			=> bot::$user_id,
		// 	"option_cat"		=> "telegram_temp_". bot::$user_id,
		// 	"option_key"		=> "inline_message_id",
		// 	"option_value"		=> $inline_message_id,
		// 	"option_meta" 		=> json_encode(['type' => NULL])
		// 	]);
	}

}
?>