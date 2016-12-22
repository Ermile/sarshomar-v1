<?php
namespace lib\utility\answers;

trait insert
{
/**
	 * insert answers to options table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args)
	{

		// set key of option table to sort answer
		// @example the poll have 3 answer
		// who we save this answers to table ?
		// [options table] : 	cat 				kye 		value  		 (the fields)
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_3		[answer 3]
		// $_args =
		// [
		// 	'poll_id' => 1,
		// 	'answers' =>
		// 		[
		// 			'txt'   => 'answer one',
		// 			'type'  => 'audio'|'emoji',
		// 			'desc'  => 'description',
		// 			'true'  => 'true|false',
		// 			'score' => 10
		// 		],
		// 		[
		// 			'txt'   => 'answer two',
		// 			'type'  => 'audio',
		// 			'desc'  => 'description',
		// 			'true'  => 'true|false',
		// 			'score' => 10
		// 		]
		// 	];
		$answers   = [];
		$opt_meta = [];
		// answers key : opt_1, opt_2, opt_[$i], ...
		$i = 0;
		foreach ($_args['answers'] as $key => $value)
		{

			$meta = [
					'desc'  => isset($value['desc'])  ? $value['desc']  : '',
					'true'  => isset($value['true'])  ? $value['true']  : '',
					'score' => isset($value['score']) ? $value['score'] : '',
					'type'  => isset($value['type'])  ? $value['type']  : ''
					];

			// answers key : opt_1, opt_2, opt_[$i], ...
			$i++;
			$answers[] =
			[
				'post_id'      => $_args['poll_id'],
				'option_cat'   => 'poll_' . $_args['poll_id'],
				'option_key'   => 'opt_' .  $i,
				'option_value' => $value['txt'],
				'option_meta'  => json_encode($meta, JSON_UNESCAPED_UNICODE)
			];

			$opt_meta[] =
			[
				'key'  => 'opt_'.  $i,
				'txt'  => isset($value['txt'])  ? $value['txt']  : '',
				'type' => isset($value['type']) ? $value['type'] : ''
			];

		}

		$return = \lib\db\options::insert_multi($answers);

		// creat meta of options table for one answers record
		// every question have more than two json param.
		// opt : answers of this poll
		// answers : count of people answered to this poll
		// desc : description of answers
		$meta = ['opt' 	=> $opt_meta];

		// merge old meta and new meta in post meta
		$set_meta = \lib\db\polls::merge_meta($meta, $_args['poll_id']);
		return $return;
	}
}
?>