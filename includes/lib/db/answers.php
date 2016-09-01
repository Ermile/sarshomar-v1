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

		// $myAnswersList = json_encode($_answersList, JSON_UNESCAPED_UNICODE);

		$answers = [];
		// set key of opt like this -> opt_0, opt_1, opt_2, opt_3_true
		$i = 0;

		foreach ($_args['answers'] as $key => $value) {
			if($value) {
				// set key of opt like this -> opt_0, opt_1, opt_2, opt_3_true
				$i++;
				$answers[] = [
							'post_id'      => $_args['post_id'],
							'option_cat'   => 'poll_' . $_args['post_id'],
							'option_key'   => 'opt_' .  $i,
							'option_value' => $value,
							'option_meta'  => json_encode($value, JSON_UNESCAPED_UNICODE)
							];

			}
		}

		$return = \lib\db\options::insert_multi($answers);
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
	 * @param      <type>  $_post_id  The post identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_post_id) {
		$query = "
				SELECT
					id,
					option_value as 'txt',
					option_key   as 'key'
				FROM options
				WHERE
					post_id = $_post_id AND
					option_cat LIKE 'poll_{$_post_id}' AND
					option_key LIKE 'opt%'  AND
					user_id IS NULL
				";
		return \lib\db\options::select($query, "get");
	}


	/**
	 * save user answer into options table
	 * @param  [type] $_user_id [description]
	 * @param  [type] $_post_id [description]
	 * @param  [type] $_answer  [description]
	 * @return [type]           [description]
	 */
	public static function save($_user_id, $_post_id, $_answer, $_answer_txt = null)
	{
		// set status of skip answers to disable
		$status = 'enable';
		if($_answer < 0)
		{
			$status = 'disable';
		}
		$meta =
		[
			'question'   => $_post_id,
			'answer'     => $_answer,
			'answer_txt' => $_answer_txt,
			'date'       => date('Y-m-d H:i:s'),
		];
		$option_data =
		[
			'user_id'       => $_user_id,
			'post_id'       => $_post_id,
			'option_cat'    => 'poll_' . $_post_id,
			'option_key'    => 'answer_' . $_user_id,
			'option_value'  => $_answer,
			'option_meta'   => json_encode($meta, JSON_UNESCAPED_UNICODE),
			'option_status' => $status
		];
		// save in options table and if successful return session_id
		$result = \lib\db\options::insert($option_data);

		// save answered count
		self::set_count_answered($_post_id);

		return $result;
	}


	public static function set_count_answered($_poll_id){
		$query = "
				SELECT
					count(id) as 'count'
				FROM
					options
				WHERE
					user_id IS NOT NULL AND
					option_cat = 'poll_{$_poll_id}' AND
					option_key LIKE 'answer%'
				";
		$count = \lib\db\posts::select($query, 'get');
		$count = $count[0]['count'];

		\lib\db\polls::update(['post_count' => $count], $_poll_id);
	}


}

?>