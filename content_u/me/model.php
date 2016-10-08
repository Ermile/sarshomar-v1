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
		$me['displayname'] = $displayname;
		return $me;
	}


	/**
	 * post data and update or insert me data
	 */
	public function post_me()
	{
		$user_id = $this->login('id');

		$displayname       = utility::post("displayname");
		$displayname = \lib\db\users::set_displayname($user_id, $displayname);

		$support_filter = \lib\db\filters::support_filter();

		$post = utility::post();

		$args = [];
		foreach ($post as $key => $value) {
			if(\lib\db\filters::support_filter($key))
			{
				$args[$key] = $value;
			}
		}

		if(isset($args['birthdate']))
		{
			$age = self::get_age($args['birthdate']);
			$args['age'] = $age;
		}

		$profiles = \lib\db\profiles::set_profile_data($user_id, $args);

		if($profiles)
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