<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;

trait set_language
{
	public static function set_language($_query, $_data_url)
	{
		self::edit_message(['text' => 'yohahahah']);
		return ['text' => 'hiii'];
	}
}
?>