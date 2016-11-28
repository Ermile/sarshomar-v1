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
			$title = '[' . html_entity_decode($this->class->query_result['title']) . ']' .
			'(https://sarshomar.com/sp_' . $this->class->short_link .')';
		}
		else
		{
			$title = html_entity_decode($this->class->query_result['title']);
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

		$this->message['chart'] = utility::calc_vertical($this->poll_answer);
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
				$poll_list .= ' (' . $value['answer_count'] . ")";
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
		$this->message['telegram_link'] = '[' . T_('Answer link') . ']'.
		'(https://telegram.me/sarshomarBot?start=sp_' . $this->class->short_link . ')';
	}

	public function add_telegram_tag()
	{
		$this->message['telegram_tag'] = '#Sarshomar';
	}

	public function set_telegram_result($_answer_id = null)
	{
		$answer_id = $this->set_answer_id($_answer_id);

		$this->class->telegram_result = \lib\utility\stat_polls::get_telegram_result($this->class->poll_id);
		$poll_result = $this->class->telegram_result;
		if(!$poll_result)
		{
			$poll_result = $this->class->query_result;
			foreach ($poll_result['meta']['opt'] as $key => $value) {
				$poll_result['result'][$value['txt']] = 0;
			}
		}
		$this->set_poll_list($poll_result['result'], $answer_id);
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
				$poll_list[] = ['emoji'=> '✅ ', 'text' => $key, 'answer_count' => $value];
			}
			else
			{
				$poll_list[] = ['emoji'=> $row[$count], 'text' => $key, 'answer_count' => $value];
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

	public function make()
	{
		return join($this->message, "\n");
	}

	public function add($_key, $_message)
	{
		$this->message[$_key] = $_message;
	}
}
?>