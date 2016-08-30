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
		foreach ($_args['answers'] as $key => $value) {
			if($value) {
				$answers[] = [
							'post_id'      => $_args['post_id'],
							'option_cat'   => 'poll',
							'option_key'   => 'opt',
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
					option_value as 'value'
				FROM options
				WHERE
					post_id = $_post_id AND
					option_cat = 'poll' AND
					option_key = 'opt'  AND
					user_id IS NULL
				";
		return \lib\db\options::select($query, "get");
	}
}

?>