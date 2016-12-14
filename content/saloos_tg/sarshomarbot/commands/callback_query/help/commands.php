<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
trait commands{
	public static function commands($_query, $_data_url)
	{
		$text = T_("To have rapid access to any related sections, besides the available buttons, you can also use the following instructions.");
		$text .= "\n";
		$text .= "/ask";
		$text .= "\n";
		$text .= "/create";
		$text .= "\n";
		$text .= "/language";
		$text .= "\n";
		$text .= "/profile";
		$text .= "\n";
		$text .= "/dashboard";
		$text .= "\n";
		$text .= "/help";
		$text .= "\n";
		$text .= "/faq";
		$text .= "\n";
		$text .= "/commands";
		$text .= "\n";
		$text .= "/feedback";
		$text .= "\n";
		$text .= "/privacy";
		$text .= "\n";
		$text .= "/about";
		return [
			'text' => $text,
			"reply_markup"	=> [
				"inline_keyboard" => [
					[
						['text' => T_('Help'), 'callback_data' => 'help/home'],
					]
				]
			]
		];
	}
}
?>