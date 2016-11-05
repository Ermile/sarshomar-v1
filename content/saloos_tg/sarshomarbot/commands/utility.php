<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;

class utility
{
	public static function response_expire($_key)
	{
		$options = ["key" => $_key];
		return [function($_response, $_data, $_options)
		{
			if($_response->ok)
			{
				$_response->result->original_text = $_data['text'];
				session::set('expire', 'inline_cache', $_options['key'], $_response);
			}
		}, $options];
	}

	public static function inline()
	{
		return utility\inline_keyboard::add(...func_get_args());
	}
}