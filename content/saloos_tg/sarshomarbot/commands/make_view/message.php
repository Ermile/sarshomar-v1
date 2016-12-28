<?php
namespace content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
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
			$title = utility::link('https://sarshomar.com/sp_' .$this->class->short_link, $this->class->query_result['title']);
		}
		else
		{
			$title = html_entity_decode($this->class->query_result['title']);
		}
		if(isset($this->class->query_result['meta']) && isset($this->class->query_result['meta']['attachment_id']))
		{
			$attachment = \lib\db\polls::get_poll($this->class->query_result['meta']['attachment_id']);
			$url = \lib\router::$base;
			$url .= '/' . preg_replace("/^.*\/public_html\//", '', $attachment['meta']['url']);
			switch ($this->class->query_result['meta']['data_type']) {
				case 'photo':
					$emoji = '🖼';
					break;
				case 'video':
					$emoji = '📹';
					break;
				case 'audio':
					$emoji = '🔊';
					break;

				default:
					$emoji = '📝';
					break;
			}
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
		$this->set_telegram_result($_answer_id);
		$sum = array_column($this->class->telegram_result->get_result('result'), 'sum', 'key');
		$this->message['chart'] = utility::calc_vertical($sum);
	}

	public function add_poll_list($_answer_id = null, $_add_count = true)
	{
		if(!isset($this->poll_list))
		{
			$this->set_telegram_result($_answer_id);
		}
		$poll_list = '';
		foreach ($this->poll_list as $key => $value) {
			$poll_list .= $value['emoji'] . ' ' . $value['text'];
			if($_add_count)
			{
				$poll_list .= ' - ' . utility::nubmer_language($value['answer_count']);
			}
			if(end($this->poll_list) !== $value)
			{
				$poll_list .= "\n";
			}
		}
		$this->message['poll_list'] = $poll_list;
	}

	public function add_telegram_link()
	{
		$dashboard = utility::link('https://telegram.me/SarshomarBot?start=sp_' .$this->class->short_link,'⚙');
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

	public function set_telegram_result($_answer_id = null)
	{
		$answer_id = $this->set_answer_id($_answer_id);

		$this->class->telegram_result = \lib\utility\stat_polls::get_telegram_result($this->class->poll_id);
		$poll_result = $this->class->telegram_result;
		if(!$poll_result->is_ok())
		{
			$poll_result = $this->class->query_result;
			foreach ($poll_result['meta']['opt'] as $key => $value) {
				$poll_result['result'][$value['txt']] = 0;
			}
		}else
		{
			$poll_result = $poll_result->get_result('result');
		}
		$this->set_poll_list($poll_result, $answer_id);
	}

	public function set_poll_list($_poll_result, $_answer_id = null)
	{
		$poll_answer = array();
		$poll_list = array();
		$count = 0;
		$row      = $this->class::$emoji_number;
		foreach ($_poll_result as $key => $value) {
			$count++;
			$poll_answer[$count] = $value;
			if($_answer_id === $count)
			{
				$this->poll_set_answer = true;
				$poll_list[] = ['emoji'=> '✅ ', 'text' => $value['text'], 'answer_count' => $value['sum']];
			}
			else
			{
				$poll_list[] = ['emoji'=> $row[$count], 'text' => $value['text'], 'answer_count' => $value['sum']];
			}
		}
		$this->poll_list = $poll_list;
		$this->poll_answer = $poll_answer;
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
			$answer = \lib\utility\answers::is_answered($this->class->user_id, $this->class->poll_id);
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
		$count = $this->class->telegram_result->get_result('count_answered');
		switch ($_type) {
			case 'valid':
				$text = T_("Valid answer is:") . $count['valid'];
				break;
			case 'invalid':
				$text .= utility::link('https://telegram.me/SarshomarBot?start=faq_5', T_("Invalid") . '(' . $count['invalid'] .')');
				break;
			case 'sum_invalid':
				$text = '👥' .utility::nubmer_language($count['sum']) . ' ';
				$text .= utility::link('https://telegram.me/SarshomarBot?start=faq_5', '❗️' . utility::nubmer_language($count['invalid']));
				break;
			case 'sum_valid':
				$text = T_("Sum") . '(' . $count['sum'] .') ';
				$text .= T_("Valid") . '(' . $count['valid'] .')';
				break;

			default:
				$text = T_("Valid") . '(' . $count['valid'] .') ';
				$text .= utility::link('https://telegram.me/SarshomarBot?start=faq_5', T_("Invalid") . '(' . $count['invalid'] .')');
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