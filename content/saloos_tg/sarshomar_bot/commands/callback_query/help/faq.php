<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query\help;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\utility;
trait faq{

	public static function faq($_query = null, $_data_url = null, $_id = null)
	{
		if($_data_url && !array_key_exists(2, $_data_url))
		{
			$faq_id = 1;
		}
		elseif($_data_url)
		{
			$faq_id = $_data_url[2];
		}
		else
		{
			$faq_id = $_id;
		}
		if(count(faq_text::text()) < (int) $_id)
		{
			return [];
		}
		$get_id = array_search($faq_id, array_column(faq_text::text(), 'id'));
		$faq = faq_text::text()[$get_id];

		$text = "<strong>" . T_($faq['title']) ."</strong>";
		$text .= "\n";
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
		$text .= "\n/faq_".$faq['id'];
		$text .= "\n#" . preg_replace("[\s]", '_', T_("FAQ"));
		$return = ["text" => $text];

		if(!is_null($_id))
		{
			$return['parse_mode'] = 'HTML';
			return $return;
		}
		else
		{
			$total_page = count(faq_text::text());
			$inline_keyboard = [];
			if($total_page > 1)
			{
				if($get_id > 1)
				{
					$inline_keyboard[0][] = ["text" => "⏮", "callback_data" => "help/faq/1"];
				}
				if($get_id > 0)
				{
					$inline_keyboard[0][] = ["text" => "◀️", "callback_data" => "help/faq/" . faq_text::text()[$get_id -1]['id']];
				}


				if($get_id < $total_page-1)
				{
					$inline_keyboard[0][] = ["text" => "▶️", "callback_data" => "help/faq/" . (faq_text::text()[$get_id +1]['id'])];
				}
				if(($get_id +2) < $total_page)
				{
					$inline_keyboard[0][] = ["text" => "⏭", "callback_data" => "help/faq/" . $total_page];
				}
			}
			$inline_keyboard[][] = ['text' => T_('Help'), 'callback_data' => 'help/home'];
			$return['reply_markup'] = ['inline_keyboard' => $inline_keyboard];
			$return['response_callback'] = utility::response_expire('help');
			return $return;
		}
	}
}
?>