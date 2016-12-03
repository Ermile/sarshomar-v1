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
	 */
	public static function get_telegram_result($_poll_id)
	{
		// get the poll to find the opt
		$poll = \lib\db\polls::get_poll($_poll_id);
		$meta = [];
		if(isset($poll['meta']))
		{
			$meta = $poll['meta'];
		}

		// get the opt of polls
		$opt = [];
		if(isset($meta['opt']) && is_array($meta['opt']))
		{
			$opt = $meta['opt'];
		}

		$field = ['total','result'];

		// the valid answers
		$valid_answers = [];
		$valid_result = \lib\db\pollstats::get($_poll_id, ['field' => $field, 'validation' => 'valid']);
		if(isset($valid_result['result']) && is_array($valid_result['result']))
		{
			$valid_answers = $valid_result['result'];
		}

		$invalid_answers = [];
		$invalid_result = \lib\db\pollstats::get($_poll_id, ['field' => $field, 'validation' => 'invalid']);
		if(isset($invalid_result['result']) && is_array($invalid_result['result']))
		{
			$invalid_answers = $invalid_result['result'];
		}

		$result                   = [];
		$result['count']          = isset($valid_result['total']) ? $valid_result['total'] : 0;
		$result['invalid_count']  = isset($invalid_result['total']) ? $invalid_result['total'] : 0;
		$result['title']          = $poll['title'];
		$result['url']            = $poll['url'];
		$result['result']         = self::process($opt, $valid_answers);
		$result['invalid_result'] = self::process($opt, $invalid_answers);
		return $result;
	}


	/**
	 * process the telegram chart
	 * the syntax is 	'opt_text' => [count answered],
	 * 					'opt_text' => [count answered],
	 * 					...
	 *
	 * @param      <type>   $_opt      The option
	 * @param      integer  $_answers  The answers
	 *
	 * @return     array    ( description_of_the_return_value )
	 */
	private static function process($_opt, $_answers)
	{
		$final_result = [];
		foreach ($_opt as $key => $value)
		{
			$opt_key = $value['key'];
			$final_result[$value['txt']] = 0;
			if(!array_key_exists($opt_key, $_answers))
			{
				continue;
			}
			$final_result[$value['txt']] = $_answers[$opt_key];
		}
		return $final_result;
	}
}
?>