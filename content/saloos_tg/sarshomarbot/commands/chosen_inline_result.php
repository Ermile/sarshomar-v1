<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\handle;

class chosen_inline_result
{
	public static function start($_query = null)
	{
		\lib\storage::set_disable_edit(true);
		if(!isset($_query['inline_message_id']))
		{
			return ;
		}
		$inline_message_id = $_query['inline_message_id'];
		$result = explode(':', $_query['result_id']);
		if(count($result) < 2)
		{
			return [];
		}
		$result_id = $result[0];
		$post_id = \lib\utility\shortURL::decode($result[1]);
		\lib\db\options::insert([
			'user_id' 		=> bot::$user_id,
			'post_id'		=> $post_id,
			'option_cat'	=> 'telegram',
			'option_key'	=> 'subport',
			'option_value'	=> $result_id,
			'option_meta'	=> $inline_message_id,
			]);
		if(isset($result[2]))
		{
			\lib\db\options::insert([
			'user_id' 		=> bot::$user_id,
			'post_id'		=> $post_id,
			'option_cat'	=> 'telegram',
			'option_key'	=> 'subport_flag',
			'option_value'	=> $result_id,
			'option_meta'	=> $inline_message_id
			]);
		}
		return [];
	}
}
?>