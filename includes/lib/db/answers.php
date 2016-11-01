<?php
namespace lib\db;

class answers
{
	/**
	 * insert answers to options table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args){

		// set key of option table to sort answer
		// @example the poll have 3 answer
		// who we save this answers to table ?
		// [options table] : 	cat 				kye 		value  		 (the fields)
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_3		[answer 3]
		$answers   = [];
		$opt_meta = [];
		// answers key : opt_1, opt_2, opt_[$i], ...
		$i = 0;
		foreach ($_args['answers'] as $key => $value) {

			$meta = [
					'desc'  => '',
					'true'  => $value['true'],
					'point' => $value['point'],
					'type'  => $value['type']
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
				'key'  => 'opt_' .  $i,
				'txt'  => $value['txt'],
				'type' => $value['type']
			];

		}

		$return = \lib\db\options::insert_multi($answers);

		// creat meta of options table for one answers record
		// every question have more than two json param.
		// opt : answers of this poll
		// answers : count of people answered to this poll
		// desc : description of answers
		$meta =
		[
			'opt'     	=> $opt_meta,
			'answers' 	=> ''
		];

		// merge old meta and new meta in post meta
		$set_meta = \lib\db\polls::merge_meta($meta, $_args['poll_id']);

		return $return;
	}

	public static function update($_args, $_id) {
		if(!isset($_args['meta'])){
			$_args['option_meta'] = json_encode($_args, JSON_UNESCAPED_UNICODE);
		}

		return \lib\db\options::update($_args, $_id);
	}


	/**
	 * get post id and return opt of this post
	 *
	 * @param      <type>  $_poll_id  The post identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id) {
		$query = "
				SELECT
					*
				FROM
					options
				WHERE
					post_id = $_poll_id AND
					option_cat LIKE 'poll_{$_poll_id}' AND
					option_key LIKE 'opt%'  AND
					user_id IS NULL
				-- answers::get()
				";
		$result = \lib\db\options::select($query, "get");
		return \lib\utility\filter::meta_decode($result);
	}


	/**
	 * save user answer into options table
	 * @param  [type] $_user_id [description]
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_answer  [description]
	 * @return [type]           [description]
	 */
	public static function save($_user_id, $_poll_id, $_answer, $_answer_txt = null)
	{

		$num_of_opt_kye = preg_split("/\_/", $_answer);
		$num_of_opt_kye = isset($num_of_opt_kye[1]) ? $num_of_opt_kye[1]: 0;
		// insert data to polldetails table
		$insert_polldetails =
		"
			INSERT INTO
				polldetails
			SET
				user_id = $_user_id,
				post_id = $_poll_id,
				opt     = '$num_of_opt_kye',
				type    = NULL,
				txt     = '$_answer_txt',
				profile =
				(
					SELECT
						CONCAT('{', GROUP_CONCAT(CONCAT('\"', option_key, '\":\"',  option_value, '\"')), '}')
					FROM
						options
					WHERE
						user_id    = $_user_id AND
						option_cat = 'user_detail_$_user_id'
				),
				visitor_id = NULL
				-- answers::save()
		";
		$result = \lib\db::query($insert_polldetails);


		// save the poll lucked by profile
		// update users profile
		$set_profile_by_poll =
		[
			'poll_id' => $_poll_id,
			'opt_key' => $_answer,
			'user_id' => $_user_id
		];
		$update_profile = \lib\db\profiles::set_profile_by_poll($set_profile_by_poll);

		// set dashboard data
		if($_answer == 'opt_0')
		{
			\lib\db\profiles::set_dashboard_data($_user_id, "poll_skipped");
		}
		else
		{
			\lib\db\profiles::set_dashboard_data($_user_id, "poll_answered");
		}

		\lib\db\profiles::people_see_my_poll($_user_id, $_poll_id, $_answer);


		if($result)
		{
			// save answered count
			$set_poll_result =
			[
				'poll_id' => $_poll_id,
				'opt_key' => $_answer,
				'user_id' => $_user_id
			];
			\lib\db\stat_polls::set_poll_result($set_poll_result);
		}

		// set status of skip answers to disable
		// $status = 'enable';
		// if($_answer < 0 )
		// {
		// 	$status = 'disable';
		// }
		// $meta =
		// [
		// 	'question'   => $_poll_id,
		// 	'answer'     => $_answer,
		// 	'answer_txt' => $_answer_txt,
		// 	'date'       => date('Y-m-d H:i:s'),
		// ];
		// $option_data =
		// [
		// 	'user_id'       => $_user_id,
		// 	'post_id'       => $_poll_id,
		// 	'option_cat'    => 'poll_' . $_poll_id,
		// 	'option_key'    => 'answer_' . $_user_id,
		// 	'option_value'  => $_answer,
		// 	'option_meta'   => json_encode($meta, JSON_UNESCAPED_UNICODE),
		// 	'option_status' => $status
		// ];

		// // save in options table and if successful return session_id
		// $result = \lib\db\options::insert($option_data);

		// // if error in insert we need to update record
		// if(!$result)
		// {
		// 	/**
		// 	 * check if this pull can update answer update else return false
		// 	 */
		// 	if("can update answer of this poll")
		// 	{
		// 		\lib\db\options::update_on_error($option_data);
		// 	}
		// 	else
		// 	{
		// 		\lib\debug::error(T_("you are answered to this poll"));
		// 	}
		// }

		// the key to update stat of this poll
		// when user update his answer we shud not update poll stat

		return $result;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function delete($_poll_id)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_status = 'disable'
			WHERE
				options.post_id = $_poll_id AND
				options.option_key LIKE 'opt%' AND
				options.user_id IS NULL
			-- answers::delete()
		";
		return \lib\db::query($query);
	}


	/**
	 * check the user answered to this poll or no
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_answered($_user_id, $_poll_id)
	{
		$query =
		"
			SELECT
				id
			FROM
				polldetails
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
			LIMIT 1
			-- answers::is_answered()
			-- check user is answered to this poll or no
		";
		$result = \lib\db::get($query, 'id', true);
		if($result)
		{
			return true;
		}
		return false;
	}
}
?>