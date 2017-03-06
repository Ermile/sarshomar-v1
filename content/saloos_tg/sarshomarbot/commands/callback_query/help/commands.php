<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
trait commands{
	public static function commands($_query, $_data_url)
	{
		$text = T_("To have rapid access to any related sections, besides the available buttons, you can also use the following instructions.");
		$text .= "\n";
		$text .= "/ask " . T_("Ask me");
		$text .= "\n";
		$text .= "/new " . T_("Create new poll");
		$text .= "\n";
		$text .= "/polls " . T_("My polls");
		$text .= "\n";
		$text .= "/profile " . T_("View Profile");
		$text .= "\n";
		$text .= "/help " . T_("Help center");
		$text .= "\n";
		$text .= "/faq " . T_("FAQ");
		$text .= "\n";
		$text .= "/language " . T_("Change language");
		$text .= "\n";
		$text .= "/dashboard " . T_("Dashboard menu");
		$text .= "\n";
		$text .= "/commands " . T_("Commands");
		$text .= "\n";
		$text .= "/feedback " . T_("Send Feedback");
		$text .= "\n";
		$text .= "/privacy " . T_("Privacy");
		$text .= "\n";
		$text .= "/about " . T_("About us");
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