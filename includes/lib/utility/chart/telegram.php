<?php
namespace lib\utility\chart;

trait telegram
{

	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_value   [description]
	 * @param  [type] $_key     [description]
	 * @return [type]           [description]
	 * @example return array like this:
	 * [the poll result merge by this array]
	 * 'count_answered' =>
	 *	 [
	 *    'valid'   =>  44,
	 *    'invalid' =>  2,
	 *    'sum'     =>  46,
	 *   ]
	 * 'result' =>
	 *	[
	 *    1 =>
	 *		[
	 *        'text'    =>  'text 1',
	 *        'key'     =>  '1',
	 *        'type'    =>  'text',
	 *        'valid'   =>  31,
	 *        'invalid' =>  0,
	 *        'sum'     =>  31,
	 *      ]
	 *    2 =>
	 *		[
	 *        'text'    =>  'text 2',
	 *        'key'     =>  '2',
	 *        'type'    =>  'text',
	 *        'valid'   =>  9,
	 *        'invalid' =>  1,
	 *        'sum'     =>  10,
	 *      ]
	 */
	public static function get_telegram_result($_poll_id, $_get_raw = false)
	{
		// get the poll to find the opt
		$poll = \lib\db\polls::get_poll($_poll_id);

		// get the opt of polls
		$opt = [];

		$opt = \lib\db\pollopts::get($_poll_id);

		$field = ['total','result'];

		// the valid answers
		$valid_answers = [];
		$valid_result_raw = \lib\db\pollstats::get($_poll_id, ['field' => $field, 'validation' => 'valid']);
		if(isset($valid_result_raw['result']) && is_array($valid_result_raw['result']))
		{
			$valid_answers = $valid_result_raw['result'];
		}

		$invalid_answers = [];
		$invalid_result_raw = \lib\db\pollstats::get($_poll_id, ['field' => $field, 'validation' => 'invalid']);
		if(isset($invalid_result_raw['result']) && is_array($invalid_result_raw['result']))
		{
			$invalid_answers = $invalid_result_raw['result'];
		}

		// the example return of this foreach in firts of this function seted
		$result = [];
		$i = 1;
		foreach ($opt as $key => $value)
		{
			$sum = 0;
			// $result[$i]['text'] = isset($value['title'])  ? $value['title']: null;
			// $result[$i]['key']  = isset($value['key'])  ? $value['key']: null;
			// $result[$i]['type'] = isset($value['type']) ? $value['type']: null;
			$value['key']                = 'opt_'. $key;

			$opt_key = isset($value['key']) ? $value['key'] : null;
			if(array_key_exists($opt_key, $valid_answers))
			{
				$result[$i]['valid'] = $valid_answers[$opt_key];
				$sum += $valid_answers[$opt_key];
			}
			else
			{
				$result[$i]['valid'] = 0;
			}

			if(array_key_exists($opt_key, $invalid_answers))
			{
				$result[$i]['invalid'] = $invalid_answers[$opt_key];
				$sum += $invalid_answers[$opt_key];
			}
			else
			{
				$result[$i]['invalid'] = 0;
			}
			$result[$i]['sum'] = $sum;
			$i++;
		}

		$valid_count            = isset($valid_result_raw['total']) ? $valid_result_raw['total'] : 0;
		$invalid_count          = isset($invalid_result_raw['total']) ? $invalid_result_raw['total'] : 0;
		$sum_count              = intval($valid_count) + intval($invalid_count);

		$poll['count_answered'] = ['valid' => $valid_count, 'invalid' => $invalid_count , 'sum' => $sum_count];
		$poll['result']         = array_values($result);

		if($_get_raw)
		{
			return ['count_answered' => $poll['count_answered'], 'result' => $poll['result']];
		}
		return self::status(true)->set_result($poll);
	}

}
?>