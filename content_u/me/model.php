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

		$name  = utility::post("name");
		$value = utility::post("value");
		if($name && $value)
		{
			$profiles = \lib\db\profiles::set_profile_data($user_id, [$name => $value]);
			if($profiles)
			{
				\lib\debug::true(T_("profile data updated"));
			}
			else
			{
				\lib\debug::error(T_("error in update profile data"));
			}
		}
		else
		{
			\lib\debug::error(T_("No data has been changed"));

		}
		// $displayname = utility::post("displayname");
		// if($displayname != '')
		// {
		// 	$displayname = \lib\db\users::set_displayname($user_id, $displayname);
		// 	if(!$displayname)
		// 	{
		// 		\lib\debug::error(T_("error in update display name"));
		// 		return false;
		// 	}
		// }

		// $email = utility::post("email");
		// if($email != '')
		// {
		// 	$email = \lib\db\users::set_email($user_id, $email);
		// 	if(!$email)
		// 	{
		// 		\lib\debug::error(T_("error in update email"));
		// 		return false;
		// 	}
		// }
	}
}
?>