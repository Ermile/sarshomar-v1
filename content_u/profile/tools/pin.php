<?php
namespace content_u\profile\tools;
use \lib\utility;
use \lib\debug;

trait pin
{
	public function set_pin()
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
	}
}
?>