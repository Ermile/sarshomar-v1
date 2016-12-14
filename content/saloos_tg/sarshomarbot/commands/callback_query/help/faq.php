<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
use \content\saloos_tg\sarshomarbot\commands\handle;
trait faq{

	public static function faq($_query, $_data_url)
	{


		handle::send_log(faq_text::$text);




		if(array_key_exists(2, $_data_url) && $_data_url[2] > 1)
		{
			if($_data_url[2] == 2)
			{
				return [
					'text' => "faq list 2/3\n4. Which devices can I use?\n5. Who are the people behind Telegram?\n6. Will you have ads? Or sell my data? Or steal my beloved and enslave my children?",
					"reply_markup"	=> [
						"inline_keyboard" => [
							[
								['text' => '◀️', 'callback_data' => 'help/faq/1'],
								['text' => T_('Help'), 'callback_data' => 'help/home'],
								['text' => '▶️', 'callback_data' => 'help/faq/3'],
							]
						]
					]
				];
			}
			elseif($_data_url[2] == 3)
			{
				return [
					'text' => "faq list 3/3\n7. How are you going to make money out of this?\n8. What are your thoughts on internet privacy?\n9. There's illegal content on Telegram. How do I take it down?",
					"reply_markup"	=> [
						"inline_keyboard" => [
							[
								['text' => '◀️', 'callback_data' => 'help/faq/2'],
								['text' => T_('Help'), 'callback_data' => 'help/home']
							]
						]
					]
				];
			}
		}
		else
		{
			return [
				'text' => "faq list 1/3\n1. Who\_ is Telegram\* for?\n2. How is Telegram different from WhatsApp?\n3. How old is Telegram?",
				"reply_markup"	=> [
					"inline_keyboard" => [
						[
							['text' => T_('Help'), 'callback_data' => 'help/home'],
							['text' => '▶️', 'callback_data' => 'help/faq/2']
						]
					]
				]
			];
		}
	}
}
?>