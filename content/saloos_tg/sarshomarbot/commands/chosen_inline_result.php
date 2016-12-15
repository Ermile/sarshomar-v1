<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\handle;

class chosen_inline_result
{
	public static function start($_query = null)
	{
		$inline_message_id = $_query['inline_message_id'];
		$id = \lib\utility\shortURL::decode($_query['result_id']);
		\lib\db\options::insert([
			'user_id' 		=> bot::$user_id,
			'post_id' 		=> $id,
			'option_cat' 	=> 'telegram',
			'option_key'	=> 'subport',
			'option_value' 	=> $inline_message_id
			]);
		return [];
	}
}
?>