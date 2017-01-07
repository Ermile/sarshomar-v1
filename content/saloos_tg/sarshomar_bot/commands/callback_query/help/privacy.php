<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query\help;
trait privacy{
	public static function privacy($_query, $_data_url)
	{
		$text = T_("Telegram robots, like ordinary users, have only access to your name, user name and user number. Moreover, receiving cell phone number and address will be possible only after obtaining permission by you. Furthermore, unlike the rumors and fallacies, except for the mentioned information, telegram robots have no possibility of penetrating your system.");

		$text .= "\n";
		$text .= T_("Your significant presence in Sarshomar helps us to reach more exact and valid results. Besides, we do our best to ensure your privacy.");

		$text .= "\n";
		$text .= T_("We hate advertising. Therefore, we ensure that Sarshomar will not create any disturbances to you and you can control the messages sent to you by Sarshomar's robot.");

		$text .= "\n";
		$text .= T_("Using Sarshomar, concentrate on your question; do not be concerned about how to ask and conduct an analysis.");

		$text .= "\n";
		$text .= T_("Sarshomar has been designed to help its clients to conduct easy and rapid opinion polls on a large scale and at low cost. We will do our best to present unprecedented and hardly accessible services and facilities. These include qualities such as Sarshomar's integration, easy access, and extensive knowledge which we are further improving, trusting God and receiving your help.");

		$text .= "\n";
		$text .= T_("We hope to fulfill your expectations in our long way ahead.");

		$text .= "\n";
		$text .= T_("Sarshomar, a product of Ermile; Developed in Iran");
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