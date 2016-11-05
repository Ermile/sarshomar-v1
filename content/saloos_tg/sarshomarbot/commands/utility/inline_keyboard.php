<?php
namespace content\saloos_tg\sarshomarbot\utility\inline_keyboard;
class inline_keyboard
{
	public static function add($_text, $_callback)
	{
		return [
		"text" => $_text,
		"callback_data" => $_callback
		];
	}
}
?>