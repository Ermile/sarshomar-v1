<?php
namespace content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \lib\telegram\tg as bot;

class inline_keyboard
{
	public $inline_keyboard = array();
	public function __construct($make_class)
	{
		$this->class = $make_class;
	}

	public function add_poll_answers($_answer = null, $_skip = false)
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
			],
			10 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [0, 4], [1, 0], [1, 1], [1, 2], [1, 3], [1, 4],
			],
			11 => [
				[0, 0] , [0, 1], [0, 2], [0, 3], [1, 0], [1, 1], [1, 2], [1, 3], [2, 0], [2, 1], [2, 2],
			]
		];
		$count_answer = count($this->class->query_result['answers']);
		if($count_answer == 0)
		{
			return ;
		}
		if($count_answer > 11)
		{
			$keyboard_map[$count_answer] = $this->make_keyboard_map($count_answer);
		}
		$row_answer = current($keyboard_map[$count_answer]);
		$last_count = $this->count();
		$sum = $this->class->message->sum_stats();
		foreach ($this->class->query_result['answers'] as $answer_key => $answer_value) {
			$callback_type = 'callback_data';

			$callback_data = 'poll/answer/' . $this->class->poll_id . '/';
			$this_row = $row_answer[0] + $last_count;
			if($answer_value['type'] == 'like')
			{
				if(is_array($_answer) && in_array('delete', $_answer['available']))
				{
					$callback_data .= 'dislike';
					$inline_emoji = "💔" . utility::nubmer_language($this->class->message->sum_stats()['total']);
				}
				else
				{
					$callback_data .= 'like';
					$inline_emoji = "❤️" . utility::nubmer_language($this->class->message->sum_stats()['total']);
				}
			}
			elseif($answer_value['type'] == 'descriptive')
			{
				$total = $sum['total'];
				$callback_data = 'https://telegram.me/sarshomarbot?start='.$this->class->poll_id . "_" . $answer_value['key'];
				$callback_type = 'url';
				$inline_emoji = "📝 " . T_("Answer");
			}
			else
			{
				$callback_data .= ($answer_value['key']);
				if(count($this->class->query_result['answers']) > 10)
				{
					$inline_emoji = utility::nubmer_language($answer_value['key']);
				}
				else
				{
					$inline_emoji = $this->class::$emoji_number[$answer_value['key']];
				}
			}

			if(isset($this->class->query_result['access_profile']))
			{
				$callback_data = 'https://telegram.me/sarshomarbot?start=' . $this->class->poll_id . '_' . $answer_value['key'];
				$callback_type = 'url';
			}
			if($this->class->poll_type == 'emoji')
			{
				$inline_emoji = $answer_value['title'];
			}
			$this->inline_keyboard[$this_row][$row_answer[1]] = [
				'text' => $inline_emoji,
				$callback_type => $callback_data
			];
			$row_answer = next($keyboard_map[$count_answer]);
		}
		if($_skip)
		{
			$this->inline_keyboard[count($this->inline_keyboard)-1][] = [
				'text' => "⏬",
				'callback_data' => 'poll/answer/' . $this->class->poll_id. '/skip'
			];
		}
	}

	public function add_guest_option($_options)
	{
		if($this->class->query_result['status'] !== 'publish')
		{
			return ;
		}
		// $this->inline_keyboard[$this->count()] = $this->get_guest_option(...$_args);
		$count = $this->count();
		$count2 = $this->count()+1;
		$options = array_merge([
			'skip' => true,
			'update' => true,
			'share' => true,
			'report' => false,
			'inline_report' => false,
			'site_link' => false,
			], $_options);
		$return = [];
		$return2 = [];

		// if($options['skip'])
		// {
		// 	$return[] = [
		// 		'text' => "⏬",
		// 		'callback_data' => 'poll/answer/' . $this->class->poll_id. '/skip'
		// 	];
		// }
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
				"switch_inline_query" => '$'.$this->class->poll_id
			];
		}
		if($options['inline_report'])
		{
			$return2[] = [
				"text" => T_("Report"),
				"callback_data" => 'poll/report/'.$this->class->poll_id
			];
		}
		elseif($options['report'])
		{
			$return[] = [
				"text" => T_("Report"),
				"url" => 'https://telegram.me/sarshomarbot?start=report_'.$this->class->poll_id
			];
		}
		if($options['site_link'])
		{
			$return[] = [
				"text" => T_("ورود به سرشمار"),
				"url" => 'https://t.me/Sarshomarbot?start=lang_fa'
			];
			// $return[] = [
			// 	"text" => T_("Share"),
			// 	"switch_inline_query" => '$'.$this->class->poll_id
			// ];
		}
		$this->inline_keyboard[$count] = $return;
		if(!empty($return2))
		{
			$this->inline_keyboard[$count2] = $return2;
		}
	}

	public function add_change_status()
	{
		$return = [];
		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' => 'array',
			'request' => [
				'id' => $this->class->query_result['id']
			]]);
		$request_status = \lib\main::$controller->model()->poll_status();
		$available_status = $request_status['available'];
		// if($request_status['current'] == 'draft')
		// {
		// 	$this->add([["text" => t_("Edit"), "callback_data" => 'poll/edit/' . $this->class->poll_id]]);
		// }
		foreach ($available_status as $key => $value) {
			$return[] = [
				"text" => T_(ucfirst($value)),
				"callback_data" => 'poll/status/' . $value . '/'.$this->class->poll_id
			];
		}
		$this->add($return);
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

	public function make_keyboard_map($_count)
	{
		$return = [];
		$ot = $_count % 4;
		$rows = floor($_count/4);
		if($_count % 5 == 0)
		{
			for ($i=0; $i <= $_count/5; $i++) {
				for ($j=0; $j < 5; $j++) {
					$return[] = [$i, $j];
				}
			}
		}
		elseif($ot == 0)
		{
			for ($i=0; $i <= $rows; $i++) {
				for ($j=0; $j < 4; $j++) {
					$return[] = [$i, $j];
				}
			}
		}
		elseif($ot != 0)
		{
			$current_row = 0;
			for ($i=0; $i < $ot; $i++) {
				$current_row++;
				for ($j=0; $j < 5; $j++) {
					$return[] = [$i, $j];
				}
			}
			for ($i=$current_row; $i <= $rows ; $i++) {
				for ($j=0; $j < 4; $j++) {
					$return[] = [$i, $j];
				}
			}
		}
		return $return;
	}

	public function make()
	{
		if(count($this->inline_keyboard) == 1 && empty($this->inline_keyboard[0])){
			return null;
		}

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