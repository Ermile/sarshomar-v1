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
		handle::send_log($this->class->query_result['stats']);
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
			$url = preg_replace("/sarshomar.com/", 'dev.sarshomar.com', $this->class->query_result['file']['url']);
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
		$sum = $this->sum_stats();
		if($this->class->poll_type == 'like' || $this->class->poll_type == 'descriptive')
		{
			return;
		}
		$this->message['chart'] = utility::calc_vertical($sum['sum_answers']);
	}

	public function add_poll_list($_answer_id = null, $_add_count = true)
	{
		$poll_list = '';
		$sum = $this->sum_stats();
		$sum = $sum['sum_answers'];
		foreach ($this->class->query_result['answers'] as $key => $value) {
			if($value['type'] == 'like' || $value['type'] == 'descriptive')
			{
				$poll_list = utf8_decode($this->class->query_result['description']);
				if($value['type'] == 'like' && $_answer_id)
				{
					$poll_list .= "\n" . T_('You liked it');
				}
				elseif($_answer_id)
				{
					$poll_list .= "\n" . T_('You liked it');
				}
				break;
			}
			elseif($_answer_id == $key+1)
			{
				$emoji = '✅';
			}
			else
			{
				$emoji = $this->class::$emoji_number[$key+1];
			}
			$poll_list .= $emoji . ' ' . $value['title'];
			if($_add_count)
			{
				$poll_list .= ' - ' . utility::nubmer_language($sum[$key+1]);
			}
			$poll_list .= "\n";

		}
		$this->message['poll_list'] = $poll_list;
	}

	public function add_telegram_link()
	{
		$dashboard = utility::tag(T_("Sarshomar")) . ' |';
		$dashboard .= utility::link('https://telegram.me/Sarshomar_bot?start=sp_' .$this->class->poll_id, '⚙');
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
		$count = $this->sum_stats();
		$text = '';
		switch ($_type) {
			case 'valid':
				$text .= T_("Valid answer is:") . $count['total_sum_valid'];
				break;
			case 'invalid':
				$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', T_("Invalid") . '(' . $count['total_sum_invalid'] .')');
				break;
			case 'sum_invalid':
				if($this->class->poll_type == 'like')
				{
					$text .= '❤️';
				}
				else
				{
					$text .= '👥';
				}
				$text .= utility::nubmer_language($count['total']) . ' ';
				if($count['total_sum_invalid'] > 0)
				{
					$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', '❗️' . utility::nubmer_language($count['total_sum_invalid']));
				}
				break;
			case 'sum_valid':
				$text .= T_("Sum") . '(' . $count['total'] .') ';
				$text .= T_("Valid") . '(' . $count['total_sum_valid'] .')';
				break;

			default:
				$text .= T_("Valid") . '(' . $count['total_sum_valid'] .') ';
				$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', T_("Invalid") . '(' . $count['total_sum_invalid'] .')');
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

	public function sum_stats()
	{
		if($this->stats)
		{
			return $this->stats;
		}
		$stats = $this->class->query_result['stats']['total'];
		$sum_valid = array_column($stats['valid'], 'value', 'key');
		$sum_invalid = array_column($stats['invalid'], 'value', 'key');
		$sum = [];
		$total_sum_valid = 0;
		$total_sum_invalid = 0;
		$total = 0;
		foreach ($sum_valid as $key => $value) {
			$sum[$key] = $value + $sum_invalid[$key];
			$total += $sum[$key];
			$total_sum_valid += $value;
			$total_sum_invalid += $$sum_invalid[$key];
		}

		$this->stats = [
			'sum_answers' 		=> $sum,
			'total_sum_valid' 	=> $total_sum_valid,
			'total_sum_invalid'	=> $total_sum_invalid,
			'total'	=> $total,
		];
		return $this->stats;
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