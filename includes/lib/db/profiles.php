<?php
namespace lib\db;

class profiles
{

	/**
	 * { function_description }
	 */
	public static function profile_data($_check = null, $_value = null)
	{
		$profile_data =
		[
			'firstname'        => null,
			'lastname'         => null,
			'gender'           => ['male', 'female'],
			'marrital'         => ['single', 'married'],
			'birthyear'        => null,
			'birthmonth'       => null,
			'birthday'         => null,
			'age'              => null,
			'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
			'rangetitle'       => ['baby', 'teenager', 'young', 'adult'],
			'uilanguage'       => null,
			'religion'         => null,
			'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			'educationtype'    => null, // only in iran [academic|howzeh]
			'course'           => null,
			'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd', 'other'],
			'howzeh'           => null,
			'howzehdegree'     => null,
			'howzehcourse'     => null,
			'educationcity'    => null,
			'employmentstatus' => ['employee', 'unemployee', 'retired'],
			'industry'         => null,
			'company'          => null,
			'jobcity'          => null,
			'jobtitle'         => null,
			'country'          => null,
			'province'         => null,
			'city'             => null,
			'village'          => null,
			'housestatus'      => ['owner', 'tenant', 'homeless'],
			'birthcountry'     => null,
			'birthprovince'    => null,
			'birthcity'        => null,
			'marrital'         => ['single', 'married'],
			'boychild'         => null,
			'girlchild'        => null,
			'internetusage'    => ['low', 'mid', 'high'],
			'skills'           => null,
			'languages'        => null,
			'books'            => null,
			'writers'          => null,
			'films'            => null,
			'actors'           => null,
			'genre'            => null,
			'musics'           => null,
			'artists'          => null,
			'sports'           => null,
			'sportmans'        => null,
			'habbits'          => null,
			'devices'          => null,
		];
		if($_check)
		{
			if($_value)
			{
				if(array_key_exists($_check, $profile_data))
				{
					if(is_array($profile_data[$_check]) && in_array($_value, $profile_data[$_check]))
					{
						return true;
					}
					elseif($profile_data[$_check] === null)
					{
						return null;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			elseif(array_key_exists($_check, $profile_data))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return $profile_data;
	}


	/**
	 * Gets the profile data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     array   The profile data.
	 */
	public static function get_profile_data($_user_id)
	{
		$result = \lib\db\termusages::usage($_user_id, 'users');
		$result = array_column($result, 'term_title', 'term_type');
		$new_result = [];
		foreach ($result as $key => $value) {
			$new_result[str_replace('users_', '', $key)] = $value;
		}
		return $new_result;
	}


	/**
	 * Gets the user filter data.
	 *
	 * @param      <type>  $_args  The argumens
	 */
	public static function get_user_filter($_user_id)
	{
		$filter_id = \lib\db\users::get($_user_id, 'filter_id');

		if(!$filter_id)
		{
			return null;
		}

		// save filter id
		$_SESSION['user']['filter_id'] = $filter_id;

		$user_filter = \lib\db\filters::get($filter_id);

		return $user_filter;
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
		if(!is_array($_args) || is_array($_user_id))
		{
			return false;
		}

		$birthyear  = null;
		$birthmonth = null;
		$birthday   = null;

		$insert_filter  = [];
		$insert_profile = [];

		foreach ($_args as $key => $value) {
			$value = trim($value);
			if(self::profile_data($key))
			{
				$check = self::profile_data($key, $value);
				// true value
				// users can set some value and the value is true
				if($check === true)
				{
					if(\lib\db\filters::support_filter($key))
					{
						$insert_filter[$key] = $value;
					}
					$insert_profile[$key] = $value;
				}
				// users can set eny value in this field
				elseif($check === null)
				{
					switch ($key) {
						case 'country':
						case 'birthcountry':
							$check = \lib\utility\countres::check($value);
							if($check)
							{
								if($key == 'country')
								{
									$insert_filter['country'] = $value;
								}
								$insert_profile[$key] = $value;
							}
							break;

						// case 'province';
						// case 'birthprovince';

						// 	break;

						// case 'educationcity':
						// case 'jobcity':
						// case 'city':
						// case 'birthcity':

						// 	break;

						default:
							$insert_profile[$key] = $value;
							break;
					}
				}
				// -------- get the age
				if($key == 'birthyear')
				{
					$birthyear = $value;
				}
				if($key == 'birthmonth')
				{
					$birthmonth = $value;
				}
				if($key == 'birthday')
				{
					$birthday = $value;
				}
				// --------------------
			}
		}

		// get the age and range from birth date
		if($birthyear)
		{
			$date_birth = $birthyear;
			if($birthmonth)
			{
				$date_birth = $date_birth. '-'. $birthmonth;
				if($birthday)
				{
					$date_birth = $date_birth. '-'. $birthday;
				}
			}
			$age                          = \lib\utility\age::get_age($date_birth);

			$insert_filter['age']         = $age;
			$insert_filter['range']       = \lib\utility\age::get_range($age);

			$insert_profile['age']        = $age;
			$insert_profile['range']      = \lib\utility\age::get_range($age);
			$insert_profile['rangetitle'] = \lib\utility\age::get_range_title($age);

		}
		// no data add
		if(empty($insert_profile))
		{
			return true;
		}

		$old_user_filter = self::get_user_filter($_user_id);
		$old_user_filter = array_filter($old_user_filter);

		unset($old_user_filter['id']);
		unset($old_user_filter['unique']);

		$insert_filter = array_merge($old_user_filter, $insert_filter);

		// get the filter id if exist
		$filter_id = \lib\db\filters::get_id($insert_filter);

		// if filter id not found insert the filter record and get the last_insert_id
		if(!$filter_id)
		{
			$filter_id = \lib\db\filters::insert($insert_filter);
			// bug !!! . filter can not be add
			if(!$filter_id)
			{
				return false;
			}
		}

		$arg    = ['filter_id' => $filter_id];
		$result = \lib\db\users::update($arg, $_user_id);

		$insert_termusages = [];

		foreach ($insert_profile as $key => $value) {
			// chech exist this profie data or no
			// if not exist insert new
			// if exist and old value = new value continue
			// if exist and old value != new value update terms and save old value in log
			$new_term_id = \lib\db\terms::get_id($value, "users_$key");
			// insrt new terms
			if(!$new_term_id || empty($new_term_id))
			{
				// new term find we need to save this to terms table
				$insert_new_terms =
				[
					'term_type'   => 'users_'. $key,
					'term_title'  => $value,
					'term_slug'   => \lib\utility\filter::slug($value),
					'term_url'    => $key. '/'. $value,
					'term_status' => 'awaiting'
				];

				$new_term_id = \lib\db\terms::insert($insert_new_terms);

				$new_term_id = \lib\db::insert_id();
				if(!$new_term_id)
				{
					$new_term_id = \lib\db\terms::get_id($value, "users_$key");
					if(!$new_term_id)
					{
						continue;
					}
				}
			}
			// check this users has similar profile data to update this
			$query =
			"
				SELECT
					termusages.*,
					terms.term_title
				FROM
					termusages
				INNER JOIN terms ON terms.id = termusages.term_id
				WHERE
					termusages.termusage_foreign = 'users' AND
					termusages.termusage_id = $_user_id AND
					terms.term_type = 'users_$key'
				LIMIT 1
			";

			$similar_terms = \lib\db::get($query, null, true);
			if($similar_terms)
			{
				if($similar_terms['term_id'] == $new_term_id)
				{
					continue;
				}

				// update termusages teble
				$old_termusave =
				[
					'term_id'           => $similar_terms['term_id'],
					'termusage_foreign' => 'users',
					'termusage_id'      => $_user_id
				];
				$new_termusave =
				[
					'term_id'           => $new_term_id,
					'termusage_foreign' => 'users',
					'termusage_id'      => $_user_id
				];
				\lib\db\termusages::update($old_termusave, $new_termusave);
				// save change log
				$log =
				[
					'user_id'   => $_user_id,
					'key'       => $key,
					'old_value' => $similar_terms['term_title'],
					'new_value' => $value
				];
				self::save_change_log($log);
			}
			else
			{
				// insert new termusages record
				$insert_termusages[] =
				[
					'term_id'           => $new_term_id,
					'termusage_foreign' => 'users',
					'termusage_id'      => $_user_id
				];
			}
		}

		if(!empty($insert_termusages))
		{
			$useage = \lib\db\termusages::insert_multi($insert_termusages);
		}

		return true;
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



	public static function set_profile_by_poll($_args)
	{
		$profile_lock =
		"
			SELECT
				option_meta AS 'lock'
			FROM
				options
			WHERE
				post_id      = $_args[poll_id] AND
				option_cat   = 'poll_$_args[poll_id]' AND
				option_key   = 'meta' AND
				option_value = 'profile'
			LIMIT 1
		";
		$profile_lock = \lib\db::get($profile_lock, 'lock', true);
		if(!$profile_lock)
		{
			return false;
		}

		$profile_data = self::get_profile_data($_args['user_id']);

		$answers      = \lib\db\answers::get($_args['poll_id']);
		$opt_value    = array_column($answers, 'option_value', 'option_key');

		if(!isset($opt_value[$_args['opt_key']]))
		{
			return false;
		}

		$user_answer  = $opt_value[$_args['opt_key']];

		// check old profile data by new data get by poll
		if(isset($profile_data[$profile_lock]))
		{
			if($profile_data[$profile_lock] == $user_answer)
			{
				return true;
			}
			elseif($profile_data[$profile_lock] == null)
			{
				// set profile data
				$profile_data[$profile_lock] = $user_answer;
				return self::set_profile_data($_args['user_id'], $profile_data);
			}
			else
			{
				// this user is not reliable
				// this user must be go to https://motamed.sarshomar.com to become activated
				$profile_data[$profile_lock] = $user_answer;
				return self::set_profile_data($_args['user_id'], $profile_data);
			}
		}
	}


	/**
	 * save users change profile in log table
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function save_change_log($_args)
	{
		if(
			!isset($_args['user_id'])    ||
			!isset($_args['key'])		 ||
			!isset($_args['old_value'])  ||
			!isset($_args['new_value'])
			)
		{
			return false;
		}

		$log_item_title = "change_$_args[key]";
		$log_item_id = \lib\db\logitems::get_id($log_item_title);
		if(!$log_item_id)
		{
			// list of priority in log item table
			// 'critical','high','medium','low'
			$log_item_priority = null;

			switch ($_args['key']) {
				case 'gender':
					$log_item_priority = 'critical';
					break;

				default:
					$log_item_priority = 'high';
					break;
			}

			$insert_log_item =
			[
				'logitem_type'     => 'users',
				'logitem_title'    => $log_item_title,
				'logitem_desc'     => $log_item_title,
				'logitem_meta'     => null,
				'logitem_priority' => $log_item_priority,
			];
			$log_item_id = \lib\db\logitems::insert($insert_log_item);
			$log_item_id = \lib\db::insert_id();
		}

		$insert_log =
		[
			'logitem_id'     => $log_item_id,
			'user_id'        => $_args['user_id'],
			'log_data'       => $_args['key'],
			'log_meta'       => "{\"old\":\"$_args[old_value]\",\"new\":\"$_args[new_value]\"}",
			'log_createdate' => date("Y-m-d H:i:s")
		];
		\lib\db\logs::insert($insert_log);
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