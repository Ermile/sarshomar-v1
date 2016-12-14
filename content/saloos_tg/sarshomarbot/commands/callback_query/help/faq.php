<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\utility;
trait faq{

	public static function faq($_query, $_data_url)
	{

		if(!array_key_exists(2, $_data_url))
		{
			$_data_url[2] = 1;
		}
		$get_id = array_search($_data_url[2], array_column(faq_text::$text, 'id'));
		$faq = faq_text::$text[$get_id];


		$text = "*" . $faq['title'] ."*";
		$text .= "\n\n";
		if(is_array($faq['text']))
		{
			$text_trans = [];
			foreach ($faq['text'] as $key => $value) {
				$text_trans[] = T_($value);
			}
			$text .= join($text_trans, "\n");
		}
		else
		{
			$text .= T_($faq['text']);
		}
		$text .= "\n/faq\_".$faq['id'];
		$text .= "\n#" . preg_replace("[\s]", '\_', T_("FAQ"));
		$return = ["text" => $text];

		$total_page = count(faq_text::$text);
		$inline_keyboard = [];
		if($total_page > 1)
		{
			if($get_id > 1)
			{
				$inline_keyboard[0][] = ["text" => T_("First"), "callback_data" => "help/faq/1"];
			}
			if($get_id > 0)
			{
				$inline_keyboard[0][] = ["text" => T_("Back"), "callback_data" => "help/faq/" . faq_text::$text[$get_id -1]['id']];
			}


			if($get_id < $total_page-1)
			{
				$inline_keyboard[0][] = ["text" => T_("Next"), "callback_data" => "help/faq/" . (faq_text::$text[$get_id +1]['id'])];
			}
			if(($get_id +2) < $total_page)
			{
				$inline_keyboard[0][] = ["text" => T_("Last"), "callback_data" => "help/faq/" . $total_page];
			}
		}
		$inline_keyboard[][] = ['text' => T_('Help'), 'callback_data' => 'help/home'];
		$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
		$return['response_callback'] = utility::response_expire('help');
		callback_query::edit_message($return);
	}
}
?>