<?php
namespace lib\db;

class profiles
{

	/**
	 * Gets the profiles data.
	 *
	 * @param      <type>  $_args  The argumens
	 */
	public static function get_profile_data($_user_id)
	{
		// if SESSION set return the SESSION
		if(isset($_SESSION['user']['profile']) && !empty($_SESSION['user']['profile']))
		{
			return $_SESSION['user']['profile'];
		}

		$query =
		"
			SELECT
				options.option_key 		AS 'key',
				options.option_value 	AS 'value',
				terms.term_title,
				terms.term_url
			FROM
				options
			LEFT JOIN terms ON
					terms.id = options.option_value AND
					options.option_cat = 'favorites'
			WHERE
				options.option_cat = 'user_detail_$_user_id'
			-- profiles::get_profile_data()
		";

		$result = \lib\db::get($query, ['key', 'value']);

		// save prifile data in SESSION
		$_SESSION['user']['profile'] = $result;

		return $result;
	}


	/**
	 * Sets the profiles data.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 * @param      <type>   $_args     The argumens
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set_profile_data($_user_id, $_args)
	{
		$old_profiles_data = self::get_profile_data($_user_id);

		$_args = array_filter($_args);

		// update all pollstate answered this users by new profie date
		$update_pollstats = [];

		$update_query = [];
		$run_all_query = true;
		foreach ($_args as $field => $value)
		{

			if(isset($old_profiles_data[$field]))
			{
				if($old_profiles_data[$field] != $value)
				{
					$where = "user_id = '$_user_id' AND option_cat = 'user_detail_$_user_id' AND option_key = '$field' ";
					$update_query =
					"
						UPDATE
							options
						SET options.option_value = '" . $_args[$field] . "'
						WHERE
							$where
						-- profiles::set_profile_data()
						";
					$update_profile = \lib\db::query($update_query);
					if($update_profile)
					{
						if(isset($_SESSION['user']['profile'][$field]))
						{
							$_SESSION['user']['profile'][$field] = $_args[$field];
							$update_pollstats[$field] = $_args[$field];
						}
					}

					if($run_all_query)
					{
						$run_all_query = $update_profile;
					}
				}
			}
			else
			{
				$value = $_args[$field];
				$insert =
				"
					INSERT INTO
						options
					SET
						post_id      = NULL,
						user_id      = '$_user_id',
						option_cat   = 'user_detail_$_user_id',
						option_key   = '$field',
						option_value = '$value'
					-- profiles::set_profile_data()
				";
				$insert_profile = \lib\db::query($insert);

				if($insert_profile)
				{
					$_SESSION['user']['profile'][$field] = $value;
					$update_pollstats[$field] = $_args[$field];
				}

				if($run_all_query)
				{
					$run_all_query = $insert_profile;
				}
			}
		}


		return $run_all_query;
	}


	/**
	 * check users profile and set all profile data to all polls answered by this users
	 * update poll stat and save new profile data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function update_pollstats($_user_id, $_pollstate)
	{

	}


	/**
	 * get count of person by group by account data
	 *
	 * @param      <type>  $_what  The what
	 *
	 * @return     <type>  The count.
	 */
	public static function get_count($_what, $_merge)
	{
		$query =
		"

			SELECT
				count(s.id)
			FROM
				options as s
			WHERE
				s.id IN (
					SELECT
						m.id
					FROM
						options as m
					WHERE
						m.option_cat LIKE 'user_detail_%' AND
	            		m.option_key = '$_merge'
	            		)
	            	 AND
	            	s.option_cat LIKE 'user_detail_%' AND
	            	s.option_key = '$_what'



		";

			// UNION SELECT
			// 	COUNT(users.id) AS 'sum',
			// 	'undefined' 	AS 'name'
			// FROM
			// 	users
		$result = \lib\db::get($query);
		// save undefined
		var_dump($result);
		exit();
		$undefined = $result['undefined'];
		unset($result['undefined']);
		$sum = array_sum($result);
		$result['undefined'] = $undefined - $sum;
		return $result;
	}
}
?>