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
		$me = \lib\utility\profiles::get_profile_data($user_id, false);
		$displayname = \lib\db\users::get_displayname($user_id);
		$email = \lib\db\users::get_email($user_id);
		$me['displayname'] = $displayname;
		$me['email'] = $email;
		return $me;
	}


	/**
	 * post data and update or insert me data
	 */
	public function post_profile()
	{
		if(utility::post("type") == 'autocomplete')
		{
			return;
			// neet to fix
			$field  = utility::post("data");
			$search = utility::post("search");
			$result = \lib\db\terms::search($search, "users_$field");
			return $result;
		}
		$user_id = $this->login('id');

		if(utility::post("type") == 'remove-tag')
		{
			$id = utility::post("id");
			$args =
			[
				'term_id'           => $id,
				'termusage_id'      => $user_id,
				'termusage_foreign' => 'users'
			];
			\lib\db\termusages::remove($args);
			return;
		}

		$name  = utility::post("name");
		$value = utility::post("value");

		if(!$name)
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
			$old_mobile = \lib\db\users::get_mobile($user_id);
			if($old_mobile == null || substr($old_mobile, 0, 5) == 'temp_')
			{
				$mobile = \lib\db\users::set_mobile($user_id, $value);
				if(!$mobile)
				{
					\lib\debug::error(T_("we can not update your mobile"));
				}
			}
		}
		else
		{
			$profiles = \lib\utility\profiles::set_profile_data($user_id, [$name => $value]);
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