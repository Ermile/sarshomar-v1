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
		if(utility::post("type") == 'autocomplete')
		{
			$field  = utility::post("data");
			$search = utility::post("search");
			$result = \lib\db\terms::search($search, "users_$field");
			return $result;
		}
		$user_id = $this->login('id');

		$name  = utility::post("name");
		$value = utility::post("value");

		if(!$name || !$value)
		{
			return false;
		}

		if($name == 'displayname')
		{
			$displayname = \lib\db\users::set_displayname($user_id, $value);
			if(!$displayname)
			{
				\lib\debug::error(T_("we can not update your displayname"));
			}
		}
		elseif($name == 'email')
		{
			$email = \lib\db\users::set_email($user_id, $value);
			if(!$email)
			{
				\lib\debug::error(T_("we can not update your email"));
			}
		}
		elseif($name == 'mobile')
		{
			$mobile = \lib\db\users::set_mobile($user_id, $value);
			if(!$mobile)
			{
				\lib\debug::error(T_("we can not update your mobile"));
			}
		}
		else
		{
			$profiles = \lib\db\profiles::set_profile_data($user_id, [$name => $value]);
			if(!$profiles)
			{
				\lib\debug::error(T_("we can not update your profiles"));
			}
		}

		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("profile data updated"));
		}
	}
}
?>