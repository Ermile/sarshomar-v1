<?php
namespace lib\utility;

class profiles
{
	use profiles\dashboard;
	use profiles\data;
	use profiles\poll_complete;
	/**
	 * { function_description }
	 */
	public static function profile_data($_check = null, $_value = null)
	{
		$profile_data =
		[
			'gender'           => ['male', 'female'],
			'marrital'         => ['single', 'married'],
			'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd'],
			'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
			'employmentstatus' => ['employee', 'unemployed', 'retired'],
			// 'internetusage'    => ['low', 'mid', 'high'],
			// 'course'           => null,
			// 'age'              => null,
			// 'country'          => null,
			// 'province'         => null,
			// 'city'             => null,
			// 'housestatus'      => ['owner', 'tenant', 'homeless'],
			// 'religion'         => null,
			// 'language'         => null,
			// 'industry'         => null


			'firstname'        => null,
			'lastname'         => null,

			// 'gender'           => ['male', 'female'],
			// 'marrital'         => ['single', 'married'],
			// 'birthyear'        => null,
			// 'birthmonth'       => null,
			// 'birthday'         => null,
			// 'age'              => null,
			// 'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
			// 'rangetitle'       => ['baby', 'teenager', 'young', 'adult'],
			// 'uilanguage'       => null,
			// 'religion'         => null,
			// 'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			// 'educationtype'    => null, // only in iran [academic|howzeh]
			// 'course'           => null,
			// 'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd', 'other'],
			// 'howzeh'           => null,
			// 'howzehdegree'     => null,
			// 'howzehcourse'     => null,
			// 'educationcity'    => null,
			// 'employmentstatus' => ['employee', 'unemployed', 'retired'],
			// 'industry'         => null,
			// 'company'          => null,
			// 'jobcity'          => null,
			// 'jobtitle'         => null,
			// 'country'          => null,
			// 'province'         => null,
			// 'city'             => null,
			// 'village'          => null,
			// 'housestatus'      => ['owner', 'tenant', 'homeless'],
			// 'birthcountry'     => null,
			// 'birthprovince'    => null,
			// 'birthcity'        => null,
			// 'marrital'         => ['single', 'married'],
			// 'boychild'         => null,
			// 'girlchild'        => null,
			// 'internetusage'    => ['low', 'mid', 'high'],
			// 'skills'           => null,
			// 'languages'        => null,
			// 'books'            => [], // similar tags, can set some value
			// 'writers'          => [], // similar tags, can set some value
			// 'films'            => [], // similar tags, can set some value
			// 'actors'           => [], // similar tags, can set some value
			// 'genre'            => [], // similar tags, can set some value
			// 'musics'           => [], // similar tags, can set some value
			// 'artists'          => [], // similar tags, can set some value
			// 'sports'           => [], // similar tags, can set some value
			// 'sportmans'        => [], // similar tags, can set some value
			// 'habbits'          => [], // similar tags, can set some value
			// 'devices'          => [], // similar tags, can set some value
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
					elseif(is_array($profile_data[$_check]) && empty($profile_data[$_check]))
					{
						return [];
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