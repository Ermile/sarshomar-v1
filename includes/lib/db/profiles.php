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

		$filter_id = \lib\db\users::get($_user_id, 'filter_id');

		// save filter id
		$_SESSION['user']['filter_id'] = $filter_id;

		$profile_data = \lib\db\filters::get($filter_id);

		return $profile_data;
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
		$_args = array_filter($_args);

		$filters = [];

		foreach ($_args as $field => $value)
		{
			if(\lib\db\filters::support_filter($field))
			{
				$filters[$field] = $_args[$field];
			}
		}

		if(empty($filters))
		{
			return true;
		}

		$filter_id = \lib\db\filters::get_id($filters);

		if(!$filter_id)
		{
			$filter_id = \lib\db\filters::insert($filters);
			// bug !!!
			if(!$filter_id)
			{
				return false;
			}
		}

		// user not change profile
		if($_SESSION['user']['filter_id'] == $filter_id)
		{
			return true;
		}
		else
		{
			$_SESSION['user']['filter_id'] = $filter_id;
		}

		$arg = ['filter_id' => $filter_id];
		$result = \lib\db\users::update($arg, $_user_id);
		return $result;
	}


	/**
	 * Gets the dashboard data.
	 * some field in users table
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The dashboard data.
	 */
	public static function get_dashboard_data($_user_id)
	{
		// need field
		$field =
		[
			'pollanswer',
			'pollskipped',
			'point',
			'surveycount',
			'pollcount',
			'peopleanswer',
			'peopleskipped',
			'userreferred',
			'userverified'
		];
		// get all field of users record
		$result = \lib\db\users::get($_user_id, $field);
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