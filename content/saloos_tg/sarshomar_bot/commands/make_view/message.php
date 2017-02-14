<?php
namespace content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
class message
{
	public $message = array();
	public function __construct($make_class)
	{
		$this->class = $make_class;
	}

	/**
	 * create title link
	 * @param boolean $_with_link true: linked text, false:reqular text
	 */
	public function add_title($_with_link = true)
	{
		if($_with_link)
		{
			$title = utility::link('https://sarshomar.com/sp_' .$this->class->poll_id, $this->class->query_result['title']);
		}
		else
		{
			$title = html_entity_decode($this->class->query_result['title']);
		}
		if(isset($this->class->query_result['file']))
		{
			$url = 'https://dev.sarshomar.com';
			$url .= '/' . $this->class->query_result['file']['url'];
			$title = '<a href="'.$url.'">📌</a> ' . $title;
		}
		$this->message['title'] = $title;
	}

	public function add_poll_chart($_answer_id = null)
	{
		/**
		 * set user answer id
		 * @var integer
		 */
		$answer_id = $this->set_answer_id($_answer_id);

		/**
		 * set telegram result: count of poll, answers and answers text
		 */
		$stats = $this->class->query_result['stats']['total'];
		$sum_valid = array_column($stats['valid'], 'value', 'key');
		$sum_invalid = array_column($stats['invalid'], 'value', 'key');
		$sum = [];
		foreach ($sum_valid as $key => $value) {
			$sum[$key] = $value + $sum_invalid[$key];
		}
		$this->sum_stats = $sum;
		$this->message['chart'] = utility::calc_vertical($sum);
	}

	public function add_poll_list($_answer_id = null, $_add_count = true)
	{
		$poll_list = '';
		foreach ($this->class->query_result['answers'] as $key => $value) {
			$emoji = $this->class::$emoji_number[$key+1];
			$poll_list .= $emoji . ' ' . $value['title'];
			if($_add_count)
			{
				$poll_list .= ' - ' . utility::nubmer_language($this->sum_stats[$key+1]);
			}
			$poll_list .= "\n";

		}
		$this->message['poll_list'] = $poll_list;
	}

	public function add_telegram_link()
	{
		$dashboard = utility::link('https://telegram.me/Sarshomar_bot?start=sp_' .$this->class->poll_id,'⚙');
		if(isset($this->message['options']))
		{
			$this->message['options'] = $dashboard . ' ' . $this->message['options'];
		}
		else
		{
			$this->message['options'] = $dashboard;
		}
	}
	public function add_telegram_tag()
	{
		$this->message['telegram_tag'] = '#' .T_('Sarshomar');
	}

	public function set_answer_id($_answer_id = null)
	{
		if(!is_null($_answer_id) && !is_bool($_answer_id) && preg_match("/^\d$/", $_answer_id))
		{
			$_answer_id = (int) $_answer_id;
		}
		if(isset($this->answer_id) && is_int($this->answer_id))
		{
			return $this->answer_id;
		}
		elseif(is_int($_answer_id) && $_answer_id >= 0)
		{
			$this->answer_id = (int) $_answer_id;
		}
		elseif ($_answer_id === true && !isset($this->answer_id)) {
			$answer = \lib\utility\answers::is_answered(\lib\main::$controller->model()->user_id, \lib\utility\shortURL::decode($this->class->poll_id));
			$this->answer_id = (int) $answer['opt'];
		}
		else
		{
			$this->answer_id = $_answer_id;
		}
		return $this->answer_id;
	}

	public function add_count_poll($_type = 'sum_invalid')
	{
		$count = $this->class->query_result['stats']['count_answered'];
		switch ($_type) {
			case 'valid':
				$text = T_("Valid answer is:") . $count['valid'];
				break;
			case 'invalid':
				$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', T_("Invalid") . '(' . $count['invalid'] .')');
				break;
			case 'sum_invalid':
				$text = '👥' .utility::nubmer_language($count['sum']) . ' ';
				$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', '❗️' . utility::nubmer_language($count['invalid']));
				break;
			case 'sum_valid':
				$text = T_("Sum") . '(' . $count['sum'] .') ';
				$text .= T_("Valid") . '(' . $count['valid'] .')';
				break;

			default:
				$text = T_("Valid") . '(' . $count['valid'] .') ';
				$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', T_("Invalid") . '(' . $count['invalid'] .')');
				break;
		}
		if(isset($this->message['options']))
		{
			$this->message['options'] .= ' ' . $text;
		}
		else
		{
			$this->message['options'] = $text;
		}
	}

	public function make()
	{
		return join($this->message, "\n");
	}

	public function add($_key, $_message, $_status = 'end', $_pointer = null)
	{
		$find = false;
		switch ($_status) {
			case 'before':
				$new_message = [];
				foreach ($this->message as $key => $value) {
					if($key == $_pointer)
					{
						$new_message[$_key] = $_message;
						$find = true;
					}
					$new_message[$key] = $value;
				}
				if(!$find)
				{
					$new_message[$key] = $value;
				}
				$this->message = $new_message;
				break;
			case 'after':
				$new_message = [];
				foreach ($this->message as $key => $value) {
					$new_message[$key] = $value;
					if($key == $_pointer)
					{
						$find = true;
						$new_message[$_key] = $_message;
					}
				}
				if(!$find)
				{
					$new_message[$key] = $value;
				}
				$this->message = $new_message;
				break;

			default:
				$this->message[$_key] = $_message;
				break;
		}
	}
}
?>