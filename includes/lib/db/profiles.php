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
				users.gender,
				users.marrital,
				users.birthday,
				users.age,
				users.language,
				users.graduation,
				users.course,
				users.employment,
				users.business,
				users.industry,
				users.countrybirth,
				users.provincebirth,
				users.citybirth,
				users.country,
				users.province,
				users.city,
				users.parental,
				users.exercise,
				users.devices,
				users.internetusage
			FROM
				users
			WHERE
				users.id = $_user_id
			LIMIT 1
			-- profiles::get_profile_data()
		";

		$result = \lib\db::get($query, null, true);
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

		$set = [];

		foreach ($_args as $field => $value)
		{
			if(\lib\db\filters::support_filter($field))
			{
				if($_args[$field] != $old_profiles_data[$field])
				{
					$set[] = " `$field` = '". $_args[$field]. "'";
					$_SESSION['user']['profile'][$field] = $_args[$field];
				}
			}
		}
		if(empty($set))
		{
			return true;
		}
		$set = join($set, " , ");
		$query =
		"
			UPDATE
				users
			SET
				$set
			WHERE
				users.id = $_user_id
		";
		$result = \lib\db::query($query);
		return $result;
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
}
?>