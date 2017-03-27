<?php
namespace content_api\v1\profile\tools;
use \lib\utility;
use \lib\debug;

trait set
{
	public function set_user_profile($_options = [])
	{
		debug::title(T_("Can not set profile data"));
		if(!$this->user_id)
		{
			return;
		}

		$sended_profile = $this->sended_profile();
		if(!debug::$status)
		{
			return;
		}

		if(empty($sended_profile) || !$sended_profile)
		{
			debug::error(T_("No profile data was set") , 'arguments', 'profile');
		}
		$options = [];
		$options['your_self_data'] = true;

		$set_profile = \lib\utility\profiles::set_profile_data($this->user_id, $sended_profile, $options);
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
}

?>