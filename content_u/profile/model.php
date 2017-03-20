<?php
namespace content_u\profile;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get profile data to show
	 */
	public function get_profile()
	{
		$user_id           = $this->login("id");
		$me                = \lib\utility\profiles::get_profile_data($user_id, false);

		$displayname       = $this->login("displayname");
		$mobile            = $this->login("mobile");
		$email             = $this->login("email");

		$me['displayname'] = $displayname;
		$me['email']       = $email;
		$me['mobile']      = $mobile;
		return $me;
	}


	/**
	 * post data and update or insert me data
	 */
	public function post_profile()
	{
		if(!$this->login())
		{
			return debug::error(T_("You must login to set profile data"));
		}

		if(utility::post('user-pin') || utility::post('user-pin') === '')
		{
			$pin = utility::post('user-pin');
			if(!ctype_digit($pin) || intval($pin) < 1000 || intval($pin) > 9999)
			{
				debug::error(T_("Invalid pin, Try again"), 'user-pin');
				return false;
			}
			$pin = utility::hasher($pin);

			$update = \lib\db\users::update(['user_pass' => $pin], $this->login('id'));
			debug::true(T_("Pin changed"));
			return true;
		}







		return ;
		// useless code


		if(utility::post("type") == 'autocomplete')
		{
			return;
			// neet to fix
			$field  = utility::post("data");
			$search = utility::post("search");
			$result = \lib\db\terms::search($search, ['term_type' => "users_$field"]);
			return $result;
		}
		$user_id = $this->login('id');

		if(utility::post("type") == 'remove-tag')
		{
			$id = utility::post("id");

			if(!is_numeric($id))
			{
				return false;
			}

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

		if(!\lib\utility\profiles::profile_data($name))
		{
			return false;
		}

		if($name == 'displayname')
		{
			$displayname = \lib\db\users::set_displayname($user_id, $value);
			if(!$displayname)
			{
				\lib\debug::error(T_("We couldn't update your display name"));
			}
		}
		elseif($name == 'email')
		{
			$email = \lib\db\users::set_email($user_id, $value);
			if(!$email)
			{
				\lib\debug::error(T_("We couldn't update your email address"));
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
					\lib\debug::error(T_("We couldn't update your mobile number"));
				}
			}
		}
		else
		{
			$profiles = \lib\utility\profiles::set_profile_data($user_id, [$name => $value]);
			if(!$profiles)
			{
				\lib\debug::error(T_("We couldn't update your profile"));
			}
		}

		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("Profile information updated"));
		}
	}
}
?>