<?php
namespace content_u\account;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get account data to show
	 */
	public function get_account()
	{
		$user_id = $this->login("id");
		$account = \lib\db\account::get_account_data(['user_id' => $user_id]);
		$displayname = \lib\db\users::get_displayname($user_id);
		$account['displayname'] = $displayname;
		return $account;
	}


	/**
	 * post data and update or insert account data
	 */
	public function post_account()
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

		$account = \lib\db\account::set_account_data($this->login('id'), $args);

		if($account)
		{
			\lib\debug::true(T_("account data updated"));
		}
		else
		{
			\lib\debug::error(T_("error in update account data"));
		}

	}
}
?>