<?php
namespace content_u\me;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get me data to show
	 */
	public function get_me()
	{
		$user_id = $this->login("id");
		$me = \lib\db\me::get_me_data(['user_id' => $user_id]);
		$displayname = \lib\db\users::get_displayname($user_id);
		$me['displayname'] = $displayname;
		return $me;
	}


	/**
	 * post data and update or insert me data
	 */
	public function post_me()
	{
		$user_id = $this->login('id');

		$displayname       = utility::post("displayname");

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

		$displayname = \lib\db\users::set_displayname($user_id, $displayname);
		$me = \lib\db\me::set_me_data($user_id, $args);
		if($me)
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