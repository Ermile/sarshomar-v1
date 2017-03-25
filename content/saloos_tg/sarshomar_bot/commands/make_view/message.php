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
		if(isset($this->class->query_result['sarshomar']) && $this->class->query_result['sarshomar'])
		{
			// $_with_link = true;
		}
		else
		{
			$_with_link = false;
		}
		if($_with_link)
		{
			$title = utility::link('https://' . $_SERVER['SERVER_NAME'] . '/$' .$this->class->poll_id, $this->class->query_result['title']);
		}
		else
		{
			$title = $this->class->query_result['title'];
		}
		if(isset($this->class->query_result['file']))
		{
			$url = preg_replace("/\?$/", "", $this->class->query_result['file']['url']);
			$title = '<a href="'.$url.'">📌</a> ' . $title;
		}
		$this->message['title'] = $title;
		if(isset($this->class->query_result['description']) && $this->class->query_result['description'])
		{
			$this->message['desc'] = $this->class->query_result['description'] ."\n";
		}
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
		if(count($this->class->query_result['answers']) > 10)
		{
			$row = 0;
			$other_key = null;
			$overflow = [];
			$sum_answers = $sum['sum_answers'];
			arsort($sum_answers);
			foreach ($sum_answers as $key => $value) {
				if($row < 9)
				{
					$row++;
					$other_key = $key;
					$overflow[$key] = $value;
					continue;
				}
				$overflow[$other_key] += $value;
			}
			$other = $overflow[$other_key];
			unset($overflow[$other_key]);
			ksort($overflow);
			$overflow[0] = $other;
			$emoji = $this->class->poll_type == 'emoji' ? array_column($this->class->query_result['answers'], 'title', 'key') : null;
			if($emoji)
			{
				$emoji[0] = "*️⃣";
			}
			$this->message['chart'] = utility::calc_vertical($overflow, $emoji);
		}
		else
		{
			$emoji = $this->class->poll_type == 'emoji' ? array_column($this->class->query_result['answers'], 'title', 'key') : null;
			$this->message['chart'] = utility::calc_vertical($sum['sum_answers'], $emoji) . "\n";
		}
	}

	public function add_poll_list($answer = null, $_add_count = true)
	{
		if($answer)
		{
			$answer_id = array_column($answer, 'key');
		}
		else
		{
			$answer_id = [];
		}
		$poll_list = '';
		$sum = $this->sum_stats();
		$sum = $sum['sum_answers'];
		if($this->class->poll_type == 'emoji')
		{
			if(!empty(trim($poll_list)))
			{
				$this->message['poll_list'] = trim($poll_list);
			}
			return;
		}
		foreach ($this->class->query_result['answers'] as $key => $value) {
			if($value['type'] == 'like' || $value['type'] == 'descriptive')
			{
				if($value['type'] == 'like' && $answer)
				{
					// $poll_list .= "\n" . T_('Liked');
				}
				elseif($answer)
				{
					$poll_list .= "\n\n" . T_('Your answer:') . $answer[0]['descriptive'];
				}
				break;
			}
			elseif(in_array($value['key'], $answer_id))
			{
				if(count($this->class->query_result['answers']) > $this->class::$max_emoji_list)
				{
					$emoji = utility::nubmer_language($value['key']);
					if($value['key'] < 10)
					{
						$emoji = utility::nubmer_language("0") . $emoji;
					}
					$emoji .= "|✅ ";
				}
				else
				{
					$emoji = $this->class::$emoji_number[$value['key']] . '✅';
				}
			}
			else
			{
				if(count($this->class->query_result['answers']) > $this->class::$max_emoji_list)
				{
					$emoji = utility::nubmer_language($value['key']);
					if($value['key'] < 10)
					{
						$emoji = utility::nubmer_language("0") . $emoji;
					}
					$emoji .= "| ";
				}
				else
				{
					$emoji = $this->class::$emoji_number[$value['key']];
				}
			}
			$poll_list .= $emoji . ' ' . $value['title'];
			if($_add_count)
			{
				$poll_list .= ' - ' . utility::nubmer_language($sum[$value['key']]);
			}
			$poll_list .= "\n";

		}
		$this->message['poll_list'] = $poll_list;
	}

	public function add_telegram_link()
	{
		$dashboard = utility::link('https://telegram.me/Sarshomar_bot?start=' .$this->class->poll_id, '⚙' . T_("Panel")) . " | ";
		if(isset($this->message['options']))
		{
			$this->message['options'] = $dashboard . ' ' . $this->message['options'];
		}
		else
		{
			$this->message['options'] = $dashboard;
		}
		// $this->message['tag'] = utility::tag(T_("Sarshomar"));
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
				$text .= '👥';
				$text .= utility::nubmer_language($count['total']);
				// if($count['total_sum_invalid'] > 0)
				// {
				// 	$text .= utility::link('https://telegram.me/Sarshomar_bot?start=faq_5', '❗️' . utility::nubmer_language($count['total_sum_invalid']));
				// }
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
		if(isset($this->stats) && $this->stats)
		{
			return $this->stats;
		}
		$stats = $this->class->query_result['result']['answers'];
		$sum = [];
		$total_sum_valid = $this->class->query_result['result']['summary']['reliable'];
		$total_sum_invalid = $this->class->query_result['result']['summary']['unreliable'];
		$total = $this->class->query_result['result']['summary']['total'];
		foreach ($stats as $key => $value) {
			$sum[$value['key']] = $value['value'];
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