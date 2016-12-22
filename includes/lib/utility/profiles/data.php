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
		$result  = \lib\db\termusages::usage($_user_id, 'users');

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$x_key = str_replace('users_', '', $value['term_type']);
				// get the accepted value in terms for insert chart
				if($_accepted_value)
				{
					if($value['term_status'] != 'enable')
					{
						continue;
					}
					$profile[$x_key] = $value['term_title'];
				}
				else
				{
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
				}
			}
		}

		$profile['mobile'] = \lib\db\users::get_mobile($_user_id);
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
		$new_term_id = \lib\db\terms::caller("$_key:$_value");
		// insrt new terms
		if(!$new_term_id || empty($new_term_id))
		{
			// new term find we need to save this to terms table
			$term_status = 'awaiting';
			if(isset($_valus_checked_true[$_key]) && $_valus_checked_true[$_key] == $_value)
			{
				$term_status = 'enable';
			}
			// cehc termslug len
			$term_slug = \lib\utility\filter::slug($_value);
			if(strlen($term_slug) > 50)
			{
				$term_slug = substr($term_slug, 0, 49);
			}
			$insert_new_terms =
			[
				'term_type'   => 'users_'. $_key,
				'term_caller' => "$_key:$_value",
				'term_title'  => $_value,
				'term_slug'   => $term_slug,
				'term_url'    => $_key. '/'. $_value,
				'term_status' => $term_status
			];

			$new_term_id = \lib\db\terms::insert($insert_new_terms);

			$new_term_id = \lib\db::insert_id();
			if(!$new_term_id)
			{
				$new_term_id = \lib\db\terms::caller("$_key:$_value");
				if(!$new_term_id)
				{
					return false;
				}
			}
		}
		return $new_term_id;
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

			$check = self::profile_data($key, $value);
			// true value
			// users can set some value and the value is true
			if($check === true)
			{
				$valus_checked_true[$key] = $value;
				$insert_profile[$key] = $value;
			}
			// profile data has bee similar to tag and can set som one
			elseif($check === [])
			{
				// we not set this value to $profile_data
				$explode_value = explode(',', $value);
				$profile_similar_tags[$key] = $explode_value;
			}
			else
			{
				$insert_profile[$key] = $value;
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
			$insert_profile['age']        = $age;
			$insert_profile['range']      = \lib\utility\age::get_range($age);
			$insert_profile['rangetitle'] = \lib\utility\age::get_range_title($age);

		}

		// insert data in terms
		$insert_termusages = [];

		foreach ($insert_profile as $key => $value)
		{
			// chech exist this profie data or no
			// if not exist insert new
			// if exist and old value = new value continue
			// if exist and old value != new value update terms and save old value in log table
			$new_term_id = self::insert_terms($key, $value, $valus_checked_true);

			if(!$new_term_id)
			{
				continue;
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
				-- check this users has similar profile data to update this
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
				$insert_termusages =
				[
					'term_id'           => $new_term_id,
					'termusage_foreign' => 'users',
					'termusage_id'      => $_user_id
				];
				$useage = \lib\db\termusages::insert($insert_termusages);
			}
		}
		// insert profile similar tags
		if(!empty($profile_similar_tags))
		{
			foreach ($profile_similar_tags as $key => $value)
			{

				$value = array_filter($value);
				foreach ($value as $n => $tag)
				{
					$new_term_id = self::insert_terms($key, $tag);

					if(!$new_term_id)
					{
						continue;
					}

					$args =
					[
						'termusage_id'      => $_user_id,
						'termusage_foreign' => 'users',
						'term_id'           => $new_term_id
					];

					if(!\lib\db\termusages::check($args))
					{

						// insert new termusages record
						$insert_termusages =
						[
							'term_id'           => $new_term_id,
							'termusage_foreign' => 'users',
							'termusage_id'      => $_user_id
						];
						$useage = \lib\db\termusages::insert($insert_termusages);

					}
				}
			}
		}
		return true;
	}
}
?>