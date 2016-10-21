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
		$me = \lib\db\profiles::get_profile_data($user_id);
		$displayname = \lib\db\users::get_displayname($user_id);
		$email = \lib\db\users::get_email($user_id);
		$me['displayname'] = $displayname;
		$me['email'] = $email;
		return $me;
	}


	/**
	 * post data and update or insert me data
	 */
	public function post_me()
	{
		$user_id = $this->login('id');

		$displayname = utility::post("displayname");
		$displayname = \lib\db\users::set_displayname($user_id, $displayname);

		$email = utility::post("email");
		$email = \lib\db\users::set_email($user_id, $email);

		$age = self::get_age(utility::post("birthdate"));
		$range = self::get_range($age);
		$arg =
		[
			'name'            => utility::post("name"),
			'lastname'        => utility::post("lastname"),
			'gender'          => utility::post("gender"),
			'marrital_status' => utility::post("marrital_status"),
			'mobile'          => utility::post("mobile"),
			'birthdate'       => utility::post("birthdate"),
			'language'        => utility::post("language"),
			'graduation'      => utility::post("graduation"),
			'education_type'  => utility::post("education_type"),
			'course'          => utility::post("course"),
			'grade'           => utility::post("grade"),
			'degree'          => utility::post("degree"),
			'employment'      => utility::post("employment"),
			'business'        => utility::post("business"),
			'industry'        => utility::post("industry"),
			'countrybirth'    => utility::post("countrybirth"),
			'country'         => utility::post("country"),
			'provincebirth'   => utility::post("provincebirth"),
			'province'        => utility::post("province"),
			'birthcity'       => utility::post("birthcity"),
			'city'            => utility::post("city"),
			'parental'        => utility::post("parental"),
			'exercise'        => utility::post("exercise"),
			'devices'         => utility::post("devices"),
			'internetusage'   => utility::post("internetusage"),
			'favorites'       => utility::post("favorites"),
			'age'             => $age,
			'range'			  => $range
		];
		// remove empty
		$arg = array_filter($arg);

		$profiles = \lib\db\profiles::set_profile_data($user_id, $arg);

		if($profiles)
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