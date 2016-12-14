<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
trait commands{
	public static function commands($_query, $_data_url)
	{
		$text = T_("To have rapid access to any related sections, besides the available buttons, you can also use the following instructions.");
		$text .= "\n";
		$text .= T_("Ask me") . " /ask";
		$text .= "\n";
		$text .= T_("Create new poll") . " /create";
		$text .= "\n";
		$text .= T_("Change language") . " /language";
		$text .= "\n";
		$text .= T_("View Profile") . " /profile";
		$text .= "\n";
		$text .= T_("Dashboard menu") . " /dashboard";
		$text .= "\n";
		$text .= T_("Help center") . " /help";
		$text .= "\n";
		$text .= T_("FAQ") . " /faq";
		$text .= "\n";
		$text .= T_("Commands usage") . " /commands";
		$text .= "\n";
		$text .= T_("Send Feedback") . " /feedback";
		$text .= "\n";
		$text .= T_("Privacy") . " /privacy";
		$text .= "\n";
		$text .= T_("About us") . " /about";
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