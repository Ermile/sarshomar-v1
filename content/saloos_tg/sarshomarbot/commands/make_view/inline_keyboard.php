<?php
namespace content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
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
		$count_answer = count($this->class->query_result['meta']['opt']);
		$row_answer = current($keyboard_map[$count_answer]);
		$last_count = $this->count();
		foreach ($this->class->query_result['meta']['opt'] as $answer_key => $answer_value) {
			$callback_data = 'ask/poll/' . $this->class->short_link . '/' . ($answer_key +1);
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

	public function get_guest_option($_no_skip = false, $_no_update = false, $_no_share = false, $_no_report = true)
	{
		$return = [];
		if(!$_no_skip)
		{
			$return[] = [
				'text' => T_("Skip"),
				'callback_data' => 'ask/poll/' . $this->class->short_link. '/0'
			];
		}
		if(!$_no_update)
		{
			$return[] = [
				'text' => T_("Update"),
				'callback_data' => "ask/update/" . $this->class->short_link
			];
		}
		if(!$_no_share)
		{
			$return[] = [
				"text" => T_("Share"),
				"switch_inline_query" => 'sp_'.$this->class->short_link
			];
		}
		if(!$_no_report)
		{
			$return[] = [
				"text" => T_("Report"),
				"url" => 'https://telegram.me/SarshomarBot?start=report_'.$this->class->short_link
			];
		}
		return $return;
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