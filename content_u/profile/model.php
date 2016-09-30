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
		$gender            = utility::post("gender");
		$marrital_status   = utility::post("marrital_status");
		$birthdate         = utility::post("birthdate");
		$graduation        = utility::post("graduation");
		$city              = utility::post("city");
		$language          = utility::post("language");

		// $age               = self::get_age($birthdate);

		$args =
		[
			'gender'            => $gender,
			'marrital_status'   => $marrital_status,
			'birthdate'         => $birthdate,
			'graduation'        => $graduation,
			'city'              => $city,
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