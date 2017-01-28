<?php
namespace content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
class inline_keyboard
{
	public $inline_keyboard = array();
	public function __construct($make_class)
	{
		$this->class = $make_class;
	}

	public function add_poll_answers($_options = array())
	{
		$keyboard_map = [
			1 => [
				[0, 0],
			],
			2 => [
				[0, 0] , [0, 1],
			],
			3 => [
				[0, 0] , [0, 1], [0, 2],
			],
			4 => [
				[0, 0] , [0, 1], [0, 2], [0, 3],
			],
			5 => [
				[0, 0] , [0, 1], [0, 2], [1, 0], [1, 1],
			],
			6 => [
				[0, 0] , [0, 1], [0, 2], [1, 0], [1, 1], [1, 2],
			],
			7 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2],
			],
			8 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2], [1, 3]
			],
			9 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2], [2, 0], [2, 1], [2, 2],
			]
		];
		$count_answer = count($this->class->query_result['answers']);
		$row_answer = current($keyboard_map[$count_answer]);
		$last_count = $this->count();
		foreach ($this->class->query_result['answers'] as $answer_key => $answer_value) {
			$callback_data = 'ask/poll/' . $this->class->poll_id . '/' . ($answer_key +1);
			if(array_key_exists("callback_data", $_options))
			{
				if(is_object($_options['callback_data']))
				{
					$callback_data = $_options['callback_data']($callback_data);
				}
				else
				{
					$callback_data = $_options['callback_data'] . "/" . $callback_data;
				}
			}
			$this_row = $row_answer[0] + $last_count;
			$this->inline_keyboard[$this_row][$row_answer[1]] = [
				'text' => $this->class::$emoji_number[$answer_key+1],
				'callback_data' => $callback_data
			];
			$row_answer = next($keyboard_map[$count_answer]);
		}
	}

	public function add_guest_option(...$_args)
	{
		$this->inline_keyboard[$this->count()] = $this->get_guest_option(...$_args);
	}

	public function get_guest_option($_options = [])
	{
		$options = array_merge([
			'skip' => true,
			'skip_last' => false,
			'update' => true,
			'share' => true,
			'report' => false,
			'inline_report' => false,
			'poll_option' => false
			], $_options);
		$return = [];
		if($options['skip_last'])
		{
			$return[] = [
				'text' => T_("Skip"),
				'callback_data' => 'ask/poll/' . $this->class->poll_id. '/0/last'
			];
		}
		elseif($options['skip'])
		{
			$return[] = [
				'text' => T_("Skip"),
				'callback_data' => 'ask/poll/' . $this->class->poll_id. '/0'
			];
		}
		if($options['update'])
		{
			$return[] = [
				'text' => '🔄',
				'callback_data' => "ask/update/" . $this->class->poll_id
			];
		}
		if($options['share'] && $this->class->query_result['status'] == 'publish')
		{
			$return[] = [
				"text" => T_("Share"),
				"switch_inline_query" => 'sp_'.$this->class->poll_id
			];
		}
		if($options['inline_report'])
		{
			$return[] = [
				"text" => T_("Report"),
				"callback_data" => 'poll/report/'.$this->class->poll_id
			];
		}
		elseif($options['report'])
		{
			$return[] = [
				"text" => T_("Report"),
				"url" => 'https://telegram.me/\sarshomar_bot?start=report_'.$this->class->poll_id
			];
		}
		if($options['poll_option'] )
		{
			$this->get_change_status($return);
		}
		return $return;
	}

	public function get_change_status(&$_return)
	{
		if($this->class->user_id == $this->class->query_result['user_id'])
		{
			$status = $this->class->query_result['status'];
			if($status == 'publish')
			{
				$_return[] = [
					"text" => T_("Pause"),
					"callback_data" => 'poll/pause/'.$this->class->poll_id
				];
			}
			elseif($status == 'pause' || $status == 'draft')
			{
				$_return[] = [
					"text" => T_("Publish"),
					"callback_data" => 'poll/publish/'.$this->class->poll_id
				];
				$_return[] = [
					"text" => T_("Delete"),
					"callback_data" => 'poll/delete/'.$this->class->poll_id
				];
			}
		}
	}

	public function add_report_status()
	{
		$this->inline_keyboard[$this->count()] = [
			[
				'text' => T_('Lawbreaker'),
				'callback_data' => 'poll/report/' . $this->class->poll_id . '/lawbreaker'
			],
			[
				'text' => T_('Spam'),
				'callback_data' => 'poll/report/' . $this->class->poll_id . '/spam'
			]
		];
		$this->inline_keyboard[$this->count()] = [
			[
				'text' => T_('Not interested'),
				'callback_data' => 'poll/report/' . $this->class->poll_id . '/not_interested'
			],
			[
				'text' => T_('Privacy violation'),
				'callback_data' => 'poll/report/' . $this->class->poll_id . '/privacy_violation'
			]
		];
	}

	public function make()
	{
		return $this->inline_keyboard;
	}

	public function add($_inline_keyboard)
	{
		$this->inline_keyboard[$this->count()] = $_inline_keyboard;
	}

	public function count()
	{
		return count($this->inline_keyboard);
	}
}
?>