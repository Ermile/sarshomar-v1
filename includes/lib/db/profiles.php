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
		$old_profiles_data = self::get_profile_data(['user_id' => $_user_id]);

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
		// update poll state
		self::update_pollstats($_user_id, $update_pollstats);

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

		$set = [];
		$set_for_insert = [];

    	// set profile result
    	$support_filter = \lib\db\filters::support_filter();

		foreach ($_pollstate as $key => $value) {
			if(in_array($key, $support_filter))
			{
				$v = '$.' . $opt_key. '."'. $user_profile_data[$value]. '"';
				$set[] =
				"
					pollstats.$value =
				       	IF(pollstats.$value IS NULL OR pollstats.$value = '',
					       		'{\"$opt_key\":{\"$value\":1}}',
							IF(
							   JSON_EXTRACT(pollstats.$value, '$v'),
							   JSON_REPLACE(pollstats.$value, '$v', JSON_EXTRACT(pollstats.$value, '$v') + 1 ),
							   JSON_INSERT(pollstats.$value, '$.$opt_key',JSON_OBJECT(\"{$user_profile_data[$value]}\",1))
							)
						)
	        	";
	        	$set_for_insert[] = " pollstats.$value = '{\"$opt_key\":{\"{$user_profile_data[$value]}\":1}}' ";
			}
			else
			{
				// undifined
				$v = '$.' . $opt_key. '.undefined';
				$set[] =
				"
					pollstats.$value =
				       	IF(pollstats.$value IS NULL OR pollstats.$value = '',
					       		'{\"$opt_key\":{\"undefined\":1}}',
							IF(
							   JSON_EXTRACT(pollstats.$value, '$v'),
							   JSON_REPLACE(pollstats.$value, '$v', JSON_EXTRACT(pollstats.$value, '$v') + 1 ),
							   JSON_INSERT(pollstats.$value, '$.$opt_key',JSON_OBJECT(\"undefined\",1))
							)
						)
	        	";
	        	$set_for_insert[] = " pollstats.$value = '{\"$opt_key\":{\"undefined\":1}}' ";
			}
		}
		$set[] = " pollstats.total = pollstats.total + 1 ";
		$set = join($set, " , ");
		$pollstats_update_query =
		"
			UPDATE
				pollstats
			SET
				$set
			WHERE
				pollstats.post_id = $poll_id
			-- update poll stat result
			-- stat_polls::set_poll_result()
		";

		$pollstats_update = \lib\db::query($pollstats_update_query);
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$set_for_insert[] = " pollstats.post_id = $poll_id ";
			$set_for_insert[] = " pollstats.total = 1 ";
			$set_for_insert = join($set_for_insert, " , ");
			$pollstats_insert_query =
			"
				INSERT INTO
					pollstats
				SET
					$set_for_insert
				-- stat_polls::set_poll_result()
				-- insert poll stat result
			";
			$pollstats_insert = \lib\db::query($pollstats_insert_query);
		}
		var_dump($_pollstate, $_user_id);
		exit();
	}
}
?>