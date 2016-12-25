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


	/**
	 * get the homepage chart by gender and range
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function gender_chart()
	{
		$query =
		"
			SELECT
				filters.gender AS `gender`,
				filters.range AS `age_range`,
				SUM(filters.count) AS 'count'
			FROM
				filters
			WHERE
				filters.gender IS NOT NULL AND
				filters.range IS NOT NULL
			GROUP BY
			gender, age_range
		";
		$result = \lib\db::get($query);

		if(!$result || !is_array($result))
		{
			return false;
		}

		$male_female = array_column($result, 'gender');
		$male_female = array_unique($male_female);

		$categories = array_column($result, 'age_range');
		$categories = array_unique($categories);
		sort($categories);


		$tmp_resutl                   = [];

		$tmp_resutl['male']           = [];
		$tmp_resutl['male']['name']   = T_("male");
		$tmp_resutl['male']['data']   = [];

		$tmp_resutl['female']         = [];
		$tmp_resutl['female']['name'] = T_("female");
		$tmp_resutl['female']['data'] = [];

		foreach ($categories as $index => $range)
		{
			$tmp_resutl['male']['data'][$range]   = 0;
			$tmp_resutl['female']['data'][$range] = 0;
			foreach ($result as $key => $value)
			{
				if($value['age_range'] == $range)
				{
					if($value['gender'] == 'male')
					{
						$tmp_resutl['male']['data'][$range] = (int)  $value['count'];
					}

					if($value['gender'] == 'female')
					{
						$tmp_resutl['female']['data'][$range] = (int) $value['count']  * -1;
					}
				}
			}
		}

		foreach ($categories as $key => $range)
		{
			if($tmp_resutl['male']['data'][$range] == 0 && $tmp_resutl['female']['data'][$range] == 0)
			{
				unset($tmp_resutl['male']['data'][$range]);
				unset($tmp_resutl['female']['data'][$range]);
				unset($categories[$key]);
			}
		}

		$series = [];
		foreach ($tmp_resutl as $key => $value)
		{
			$series[] = ['name' => $value['name'], 'data' => array_values($value['data'])];
		}

		$return               = [];
		$return['categories'] = json_encode(array_values($categories), JSON_UNESCAPED_UNICODE);
		$return['series']     = json_encode($series, JSON_UNESCAPED_UNICODE);

		return $return;

	}
}
?>