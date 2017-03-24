<?php
namespace content_u\profile;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * check pin set
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function have_pin()
	{
		if($this->login())
		{
			$user_detail = \lib\utility\users::get($this->login('id'));
			if(isset($user_detail['user_pass']) && $user_detail['user_pass'])
			{
				return true;
			}
		}
		return false;
	}


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
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'session' => $_SESSION,
			],
		];

		if(!$this->login())
		{
			return debug::error(T_("You must login to set profile data"));
		}

		if(utility::post('old-pin') || utility::post('old-pin') === '' || utility::post('new-pin') || utility::post('new-pin') === '')
		{

			$old_pin = utility::post('old-pin');
			$new_pin = utility::post('new-pin');

			$have_pin = $this->have_pin();

			if(!$have_pin && $old_pin)
			{
				\lib\db\logs::set('user:pin:havenotoldpin:set:oldpin', $this->login('id'), $log_meta);
				debug::error(T_("You have not old pin"));
				return false;
			}

			if($have_pin)
			{
				if(!ctype_digit($old_pin) || intval($old_pin) < 1000 || intval($old_pin) > 9999)
				{
					debug::error(T_("Invalid old pin, Try again"), 'old-pin');
					return false;
				}
			}

			if($new_pin && (!ctype_digit($new_pin) || intval($new_pin) < 1000 || intval($new_pin) > 9999))
			{
				debug::error(T_("Invalid new pin, Try again"), 'new-pin');
				return false;
			}

			$user_data = \lib\utility\users::get($this->login('id'));

			if($have_pin)
			{
				if(isset($user_data['user_pass']))
				{
					$check = utility::hasher($old_pin, $user_data['user_pass'], true);
					if(!$check)
					{
						\lib\db\logs::set('user:pin:invalid:old:pin', $this->login('id'), $log_meta);
						debug::error(T_("Invalid old pin"), 'old-pin');
						return false;
					}
				}
			}
			if(!$new_pin)
			{
				$new_pin = null;
				$msg = T_("Your pin has removed");
				\lib\db\logs::set('user:pin:remove', $this->login('id'), $log_meta);
			}
			else
			{
				$new_pin = utility::hasher($new_pin, null, true);
				$msg = T_("Your pin has changed");
				\lib\db\logs::set('user:pin:change', $this->login('id'), $log_meta);
			}

			$update = \lib\db\users::update(['user_pass' => $new_pin], $this->login('id'));
			// remove all remember me
			\lib\db\options::delete([
			'user_id'    => $this->login('id'),
			'option_cat' => 'session',
			'option_key' => 'rememberme',
			]);
			debug::true($msg);
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