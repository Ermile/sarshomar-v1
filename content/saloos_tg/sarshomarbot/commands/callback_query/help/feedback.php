<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
trait feedback{
	public static function feedback($_query, $_data_url)
	{
		\content\saloos_tg\sarshomarbot\commands\step_feedback::start();
		return [
			'text' => T_("Please write to us telling your opinion about Sarshomar."),
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