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
		if(isset($_SESSION['user']['profile']))
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
				options.option_cat = 'user_detail_$user_id'
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
				}

				if($run_all_query)
				{
					$run_all_query = $insert_profile;
				}
			}
		}
		return $run_all_query;
	}
}
?>