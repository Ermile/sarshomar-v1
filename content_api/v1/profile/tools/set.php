<?php
namespace content_api\v1\profile\tools;
use \lib\utility;
use \lib\debug;

trait set
{
	public function set_user_profile($_options = [])
	{
		debug::title(T_("Can not set profile data"));
		$default_options =
		[
			'method' => 'post',
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		if(!$this->user_id)
		{
			return;
		}

		$saved_profile = self::get_user_profile();
		$sended_profile = $this->sended_profile();

		if(!debug::$status)
		{
			return;
		}

		// if($_options['method'] === 'post')
		// {
		// 	if(!empty($saved_profile))
		// 	{
		// 		debug::error(T_("You have not any profile, can not use method ':method'", ['method' => 'post']), 'method', 'api');
		// 		return false;
		// 	}
		// }

		// if($_options['method'] === 'put')
		// {
		// 	if(empty($saved_profile))
		// 	{
		// 		debug::error(T_("You have not any profile, can not use method ':method'", ['method' => 'put']), 'method', 'api');
		// 		return false;
		// 	}
		// 	// no thing!
		// }

		// if($_options['method'] === 'patch')
		// {
		// 	if(empty($saved_profile))
		// 	{
		// 		debug::error(T_("You have not any profile, can not use method ':method'", ['method' => 'patch']), 'method', 'api');
		// 		return false;
		// 	}
		// }

		if(utility::isset_request('language'))
		{
			if(!\lib\utility\location\languages::check(utility::request('language')))
			{
				debug::error(T_("Invalid arguments language") , 'language', 'arguments');
				return false;
			}
			\lib\utility\users::set_lanuage($this->user_id, utility::request('language'));
			debug::true(T_("User language changed") , 'language', 'arguments');
		}

		if(utility::isset_request('unit'))
		{
			$unit_id = \lib\db\units::get_id(utility::request('unit'));
			if(!$unit_id)
			{
				debug::error(T_("Invalid arguments unit") , 'unit', 'arguments');
				return false;
			}
			\lib\utility\users::set_unit_id($this->user_id, $unit_id);
			debug::true(T_("User unit changed") , 'unit', 'arguments');
		}

		if(is_array($saved_profile) && is_array($sended_profile))
		{
			$sended_profile = array_merge($saved_profile, $sended_profile);
		}

		if(empty($sended_profile) || !$sended_profile)
		{
			debug::error(T_("No profile data was set") , 'profile', 'arguments');
			return false;
		}

		$options = [];
		$options['your_self_data'] = true;
		$support_profile = \lib\utility\profiles::profile_data();

		$support_profile = array_keys($support_profile);

		$set_profile = \lib\utility\profiles::set_profile_data($this->user_id, $sended_profile, $options);

		if(count($sended_profile) === count($support_profile))
		{
			$this->transaction_complete_profile();
		}

		debug::title(T_("The profile data was change"));
		return;
	}


	public function sended_profile()
	{

		$support_profile = \lib\utility\profiles::profile_data();
		$sended_profile = [];
		if(is_array($support_profile))
		{
			foreach ($support_profile as $key => $value)
			{
				if(utility::isset_request($key))
				{
					if(utility::request($key))
					{
						if(is_array($value) && !empty($value))
						{
							if(!in_array(utility::request($key), $value))
							{
								debug::error(T_("Invalid profile data :key", ['key' => $key]));
								return false;
							}
							$sended_profile[$key] = utility::request($key);
						}
						elseif(is_array($value) && empty($value))
						{

						}
						elseif(is_null($value))
						{

						}
					}
				}
			}
		}
		return $sended_profile;
	}


	/**
	 * check profile data
	 *
	 */
	public function transaction_complete_profile()
	{
		$caller = \lib\db\transactionitems::caller('gift:profile:complete');
		if(isset($caller['id']))
		{

			$search_transaction =
			[
				'transactionitem_id' => $caller['id'],
				'user_id'            => $this->user_id,
			];
			$transaction_exist = \lib\db\transactions::search(null, $search_transaction);
			if(empty($transaction_exist))
			{
				\lib\db\transactions::set('gift:profile:complete', $this->user_id);
				\lib\debug::true(T_("Sarshomar's gift belongs to you for completing your profile"));
			}

		}

	}
}
?>