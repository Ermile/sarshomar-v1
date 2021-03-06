<?php
namespace lib\utility\profiles;

trait data
{

	/**
	 * Gets the profile data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     array   The profile data.
	 */
	public static function get_profile_data($_user_id, $_accepted_value = true)
	{
		$profile = [];

		if($_accepted_value)
		{
			$query =
			"SELECT * FROM terms
			INNER JOIN termusages ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = 'user_profile' AND
				termusages.termusage_id      = $_user_id AND
				termusages.termusage_status  = 'enable'
			";
			$result = \lib\db::get($query);
		}
		else
		{
			$result = \lib\db\terms::usage($_user_id, [], 'users', 'sarshomar%');
		}

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$x_key = explode(":", $value['term_caller']);
				if(isset($x_key[0]))
				{
					$x_key = $x_key[0];
				}
				else
				{
					continue;
				}

				// get the accepted value in terms for insert chart
				// if($_accepted_value)
				// {
				// 	if($value['term_status'] != 'enable')
				// 	{
				// 		continue;
				// 	}
				// 	$profile[$x_key] = $value['term_title'];
				// }
				// else
				// {
					$check_similar_tags = self::profile_data($x_key, $value['term_title']);
					if($check_similar_tags === [])
					{
						if(!isset($profile[$x_key]))
						{
							$profile[$x_key] = [];
						}
						$profile[$x_key][$value['id']] = $value['term_title'];

					}
					else
					{
						$profile[$x_key] = $value['term_title'];
					}
				// }
			}
		}

		return $profile;
	}


	/**
	 * insert terms
	 *
	 * @param      string  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	public static function insert_terms($_key, $_value, $_valus_checked_true = [])
	{
		$parent_id   = null;

		$value_slug  = \lib\utility\filter::slug($_value);
		$key_slug    = \lib\utility\filter::slug($_key);

		$new_term_id = \lib\db\terms::caller("$_key:$value_slug");
		// insrt new terms
		if(!$new_term_id || empty($new_term_id))
		{
			$new_term_id_parent = \lib\db\terms::caller("$_key");

			if(!$new_term_id_parent || empty($new_term_id_parent))
			{
				$insert_new_terms_parent =
				[
					'term_type'   => 'users',
					'term_caller' => $key_slug,
					'term_title'  => $_key,
					'term_slug'   => $key_slug,
					'term_url'    => $_key,
					'term_status' => 'awaiting'
				];

				$insert_new_terms_parent = \lib\db\terms::insert($insert_new_terms_parent);

			}
			elseif(isset($new_term_id_parent['id']))
			{
				$parent_id = $new_term_id_parent['id'];
			}
			else
			{
				return false;
			}

			if(!$parent_id)
			{
				return false;
			}

			// new term find we need to save this to terms table
			$term_status = 'awaiting';
			if(isset($_valus_checked_true[$_key]) && $_valus_checked_true[$_key] == $_value)
			{
				$term_status = 'enable';
			}
			// cehc termslug len
			$value_slug = \lib\utility\filter::slug($_value);
			if(mb_strlen($value_slug) > 50)
			{
				$value_slug = substr($value_slug, 0, 49);
			}

			$insert_new_terms =
			[
				'term_type'   => 'users',
				'term_caller' => "$_key:$value_slug",
				'term_title'  => $_value,
				'term_slug'   => $value_slug,
				'term_url'    => $_key. '/'. $value_slug,
				'term_status' => $term_status,
				'term_parent' => $parent_id,
			];

			$new_term_id = \lib\db\terms::insert($insert_new_terms);

			return $new_term_id;

			// $new_term_id = \lib\db\terms::caller("$_key:$_value");
		}
		return isset($new_term_id['id']) ? $new_term_id : false;
	}


	/**
	 * Gets the user filter data.
	 *
	 * @param      <type>  $_args  The argumens
	 */
	public static function get_user_filter($_user_id)
	{
		$filter_id = \lib\utility\users::get_filter_id($_user_id);

		if(!$filter_id)
		{
			return null;
		}

		$user_filter = \lib\db\filters::get($filter_id);

		return $user_filter;
	}



	/**
	 * fin in profile array
	 *
	 * @param      <type>  $_needle    The needle
	 * @param      <type>  $_haystack  The haystack
	 */
	private static function find_in_profile($_needle, $_haystack, $_field)
	{
		foreach ($_haystack as $key => $value)
		{
			if(isset($value[$_field]) && $value[$_field] == $_needle)
			{
				return $value;
			}
		}
		return false;
	}


	/**
	 * Sets the profile data your self.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_args     The arguments
	 * @param      array   $_options  The options
	 */
	public static function set_profile_data_your_self($_user_id, $_args, $_options = [])
	{
		$sended_caller = [];
		foreach ($_args as $key => $value)
		{
			array_push($sended_caller, "$key:$value");
		}

		$get_all_your_self_profile =
		"
			SELECT
				termusages.*,
				terms.*
			FROM
				termusages
			INNER JOIN terms ON terms.id = termusages.term_id
			WHERE
				termusages.termusage_foreign = 'user_profile' AND
				termusages.termusage_id      = $_user_id
		";

		$get_all_your_self_profile = \lib\db::get($get_all_your_self_profile);
		$must_disable = [];
		$must_enable  = [];
		$must_insert  = [];
		$saved_caller = [];

		if(is_array($get_all_your_self_profile))
		{
			$must_disable = array_column($get_all_your_self_profile, 'term_id' );
			$saved_caller  = array_column($get_all_your_self_profile, 'term_caller');
		}

		foreach ($_args as $key => $value)
		{
			$caller = "$key:$value";
			if(in_array($caller, $saved_caller))
			{
				$saved_value = self::find_in_profile($caller, $get_all_your_self_profile, 'term_caller');

				if(isset($saved_value['termusage_status']) && isset($saved_value['id']))
				{
					if($saved_value['termusage_status'] === 'disable')
					{
						array_push($must_enable, $saved_value['id']);
					}

					$find_key = array_search($saved_value['id'], $must_disable);
					if(isset($find_key))
					{
						unset($must_disable[$find_key]);
					}
				}
			}
			else
			{
				$new_term  = \lib\db\terms::caller($caller);

				if(!$new_term || !isset($new_term['id']))
				{
					continue;
				}

				$must_insert[] =
				[
					'term_id'           => $new_term['id'],
					'termusage_foreign' => 'user_profile',
					'termusage_id'      => $_user_id
				];
			}
		}

		// var_dump($must_enable, $must_disable, $must_insert); exit();

		if(!empty($must_disable))
		{
			$must_disable = implode(',', $must_disable);
			\lib\db::query("UPDATE termusages
			SET termusage_status = 'disable'
			WHERE termusage_id = $_user_id AND
			termusage_foreign = 'user_profile' AND
			term_id IN ($must_disable) ");
		}

		if(!empty($must_enable))
		{
			$must_enable = implode(',', $must_enable);
			\lib\db::query("UPDATE termusages
			SET termusage_status = 'enable'
			WHERE termusage_id = $_user_id AND
			termusage_foreign = 'user_profile' AND
			term_id IN ($must_enable) ");
		}


		if(!empty($must_insert))
		{
			\lib\db\termusages::insert_multi($must_insert);
		}
		return true;
	}



	/**
	 * Sets the profiles data.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 * @param      <type>   $_args     The argumens
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set_profile_data($_user_id, $_args, $_options = [])
	{
		if(!is_array($_args) || is_array($_user_id))
		{
			return false;
		}

		if(isset($_options['your_self_data']) && $_options['your_self_data'] === true)
		{
			self::set_profile_data_your_self($_user_id, $_args, $_options);
		}

		$birthyear  = null;
		$birthmonth = null;
		$birthday   = null;

		$insert_filter  = [];
		$insert_profile = [];

		// if value in insert filter checked and not exist in terms table
		// insert in terms table and set the status of this record 'enable'
		// because checked value and no problem
		$valus_checked_true = [];

		// some index of profile is similar tags and users can set some value in this index
		$profile_similar_tags = [];

		foreach ($_args as $key => $value)
		{
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
					$valus_checked_true[$key] = $value;
					$insert_profile[$key] = $value;
				}
				// users can set eny value in this field
				elseif($check === null)
				{
					$insert_to_filters = false;
					switch ($key)
					{
						case 'age':
							if(intval($value) > 5 && intval($value) < 90)
							{
								$insert_to_filters = true;
							}
							break;

						case 'country':
							$insert_to_filters = \lib\utility\location\countres::get('id', $value, 'name');
							break;

						case 'province':
							$insert_to_filters = \lib\utility\location\provinces::get('id', $value, 'name');
							break;

						case 'city':
							$insert_to_filters = \lib\utility\location\cites::get('id', $value, 'name');
							break;

						// case 'course':
						// case 'religion':
						// case 'language':
						// case 'industry':

					}

					if($insert_to_filters)
					{
						$valus_checked_true[$key] = $value;
						$insert_filter[$key]      = $value;
					}
					$insert_profile[$key] = $value;

				}
				// profile data has bee similar to tag and can set som one
				elseif($check === [])
				{
					// we not set this value to $profile_data
					$explode_value = explode(',', $value);
					$profile_similar_tags[$key] = $explode_value;
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
		if(!empty($insert_filter))
		{
			// get the exist filter value
			// to not empty set in field has been complete befor
			$old_user_filter = self::get_user_filter($_user_id);

			if($old_user_filter && is_array($old_user_filter))
			{
				$old_user_filter = array_filter($old_user_filter);

				unset($old_user_filter['id']);
				unset($old_user_filter['unique']);
				unset($old_user_filter['usercount']);
				unset($old_user_filter['count']);

				$insert_filter = array_merge($old_user_filter, $insert_filter);

				if(isset($_options['type']) && $_options['type'] === 'minus')
				{
					foreach ($_args as $f => $v)
					{
						if(\lib\db\filters::support_filter($f, $v))
						{
							unset($insert_filter[$f]);
						}
					}
				}
			}

			if(empty($insert_filter))
			{
				$result = \lib\db\users::update(['filter_id' => null], $_user_id);
			}
			else
			{
				// get the filter id if exist
				$filter_id = \lib\db\filters::get_id($insert_filter);

				// if filter id not found insert the filter record and get the last_insert_id
				if(!$filter_id)
				{
					$filter_id = \lib\db\filters::insert($insert_filter);
				}

				if($filter_id)
				{
					$arg    = ['filter_id' => $filter_id];
					$result = \lib\db\users::update($arg, $_user_id);
				}
			}
		}
		return true;
	}
}
?>