<?php
namespace lib\utility;

/** work with polls **/
class stat_polls
{
	use chart\get;
	use chart\set;
	use chart\telegram;

	/**
	 * return the status array
	 *
	 * @param      <type>   $_status  The status
	 * @param      boolean  $_update  The update
	 * @param      array    $_msg     The message
	 */
	public static function status($_status)
	{
		$return = new \lib\db\db_return();
		return $return->set_ok($_status);
	}

	/**
	 * get list of questions that this user answered
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function answeredInPeriod($_user_id, $_period = 6)
	{
		$qry ="SELECT count(id) as count FROM options
			WHERE
				user_id = $_user_id AND
				option_cat = 'polls_$_user_id' AND
				option_key LIKE 'answer\_%' AND
				option_status = 'enable' AND
				date_modified >= DATE_SUB(NOW(),INTERVAL $_period HOUR)
		";

		$result = (int) \lib\db::get($qry, 'count', true);
		return $result;
	}


	/**
	 * Sets the sarshomar total answered.
	 */
	public static function set_sarshomar_total_answered()
	{
		// set count of answered poll
		$stat_query =
		"
			UPDATE
				options
			SET
				options.option_value  = option_value + 1
			WHERE
				options.user_id IS NULL AND
				options.post_id IS NULL AND
				options.option_cat   = 'sarshomar_total_answered' AND
				options.option_key   = 'total_answered'
			-- stat_poll::set_sarshomar_total_answered()
		";

		$update = \lib\db::query($stat_query);
		// if can not update record insert new record
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$insert_query =
			"
				INSERT INTO
					options
				SET
					options.post_id      = NULL,
					options.user_id      = NULL,
					options.option_cat   = 'sarshomar_total_answered',
					options.option_key   = 'total_answered',
					options.option_value = 1
				-- stat_poll::set_sarshomar_total_answered()
			";
			$insert = \lib\db::query($insert_query);
		}
	}


	/**
	 * Gets the sarshomar total answered.
	 *
	 * @return     <type>  The sarshomar total answered.
	 */
	public static function get_sarshomar_total_answered()
	{
		$stat_query =
		"
			SELECT
				options.option_value AS 'count'
			FROM
				options
			WHERE
				options.user_id IS NULL AND
				options.post_id IS NULL AND
				options.option_cat   = 'sarshomar_total_answered' AND
				options.option_key   = 'total_answered'
			LIMIT 1
			-- stat_poll::get_sarshomar_total_answered()
		";
		$total = \lib\db::get($stat_query, 'count', true);
		return intval($total);
	}


	/**
	 * Gets the random poll id by tags #homepage
	 *
	 * @return     boolean  The random poll identifier.
	 */
	public static function get_random_poll_result($_options = [])
	{

		$default_options =
		[
			'validation' => 'valid'
		];
		$_options = array_merge($default_options, $_options);
		$language = \lib\define::get_language();
		$query =
		"
			SELECT
				options.post_id AS 'id'
			FROM
				options
			RIGHT JOIN
				posts
			ON
				posts.id = options.post_id AND
				posts.post_status = 'publish' AND
				posts.post_privacy = 'public' AND
				(posts.post_language IS NULL OR posts.post_language = '$language')
			WHERE
				options.option_cat    = 'homepage' AND
				options.option_key    = 'chart' AND
				options.option_status = 'enable'
			ORDER BY RAND()
			LIMIT 1
			-- get random poll id by homepage options to show in homepage
		";
		$random_poll_id = \lib\db::get($query, "id", true);
		$poll_result = self::get_result($random_poll_id, $_options);
		return $poll_result;
	}
}
?>