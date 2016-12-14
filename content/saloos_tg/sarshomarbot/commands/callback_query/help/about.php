<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
trait about{
	public static function about($_query, $_data_url)
	{
		$text = T_("What is Sarshomar?");
		$text .= "\n";
		$text .= "\n";

		$text .= T_("Sarshomar is an integrative system to develop, manage and analyze online opinion polls in an efficient manner. Using Sarshomar is convenient, quick and without common complexities; its services are also available to the public.");
		$text .= "\n";
		$text .= "\n";

		$text .= T_("Using Sarshomar, do not be concerned about how to ask and conduct an analysis, and only concentrate on your question.");
		$text .= "\n";
		$text .= "\n";

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