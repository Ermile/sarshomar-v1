<?php
namespace content_u\profile;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get profile data to show
	 */
	public function get_profile()
	{
		$user_id = $this->login("id");
		$profile = \lib\db\profile::get_profile_data(['user_id' => $user_id]);
		$displayname = \lib\db\users::get_displayname($user_id);
		$profile['displayname'] = $displayname;
		return $profile;
	}


	/**
	 * post data and update or insert profile data
	 */
	public function post_profile()
	{
		$displayname       = utility::post("displayname");
		// $mobile            = utility::post("mobile");
		$gender            = utility::post("gender");
		$marrital_status   = utility::post("marrital_status");
		$parental_status   = utility::post("parental_status");
		$exercise_habits   = utility::post("exercise_habits");
		$employment_status = utility::post("employment_status");
		$business_owner    = utility::post("business_owner");
		$industry          = utility::post("industry");
		$devices_owned     = utility::post("devices_owned");
		$internet_usage    = utility::post("internet_usage");
		$birthdate         = utility::post("birthdate");
		$range             = utility::post("range");
		$graduation        = utility::post("graduation");
		$course            = utility::post("course");
		$country_birth     = utility::post("country_birth");
		$country           = utility::post("country");
		$province_birth    = utility::post("province_birth");
		$province          = utility::post("province");
		$birthcity         = utility::post("birthcity");
		$city              = utility::post("city");
		$favorites         = utility::post("favorites");
		$language          = utility::post("language");

		$age               = self::get_age($birthdate);

		$args =
		[
			'displayname'       => $displayname,
			// 'mobile'            => $mobile,
			'gender'            => $gender,
			'marrital_status'   => $marrital_status,
			'parental_status'   => $parental_status,
			'exercise_habits'   => $exercise_habits,
			'employment_status' => $employment_status,
			'business_owner'    => $business_owner,
			'industry'          => $industry,
			'devices_owned'     => $devices_owned,
			'internet_usage'    => $internet_usage,
			'birthdate'         => $birthdate,
			'age'               => $age,
			'range'             => $range,
			'graduation'        => $graduation,
			'course'            => $course,
			'country_birth'     => $country_birth,
			'country'           => $country,
			'province_birth'    => $province_birth,
			'province'          => $province,
			'birthcity'         => $birthcity,
			'city'              => $city,
			'favorites'         => $favorites,
			'language'          => $language
		];

		$profile = \lib\db\profile::set_profile_data($this->login('id'), $args);

		if($profile)
		{
			\lib\debug::true(T_("profile data updated"));
		}
		else
		{
			\lib\debug::error(T_("error in update profile data"));
		}

	}
}
?>