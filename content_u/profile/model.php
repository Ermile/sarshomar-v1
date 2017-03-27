<?php
namespace content_u\profile;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	use tools\pin;
	use tools\profile;
	use \content_api\v1\profile\tools\get;
	use \content_api\v1\profile\tools\set;

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
		if(!$this->login())
		{
			return;
		}

		$this->user_id     = $this->login("id");

		$me                = $this->get_user_profile();

		$me['displayname'] = $this->login("displayname");
		$me['mobile']      = $this->login("mobile");
		$me['email']       = $this->login("email");

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
			debug::error(T_("You must login to set profile data"));
			return false;
		}

		$this->user_id = $this->login('id');

		if(utility::post('old-pin') || utility::post('old-pin') === '' || utility::post('new-pin') || utility::post('new-pin') === '')
		{
			$this->set_pin();
			return;
		}

		if($this->set_profile())
		{
			return;
		}




		return ;
		// useless code


		// if(utility::post("type") == 'autocomplete')
		// {
		// 	return;
		// 	// neet to fix
		// 	$field  = utility::post("data");
		// 	$search = utility::post("search");
		// 	$result = \lib\db\terms::search($search, ['term_type' => "users_$field"]);
		// 	return $result;
		// }
		// $user_id = $this->login('id');

		// if(utility::post("type") == 'remove-tag')
		// {
		// 	$id = utility::post("id");

		// 	if(!is_numeric($id))
		// 	{
		// 		return false;
		// 	}

		// 	$args =
		// 	[
		// 		'term_id'           => $id,
		// 		'termusage_id'      => $user_id,
		// 		'termusage_foreign' => 'users'
		// 	];
		// 	\lib\db\termusages::remove($args);
		// 	return;
		// }

		// $name  = utility::post("name");
		// $value = utility::post("value");

		// if(!$name)
		// {
		// 	return false;
		// }

		// if(!\lib\utility\profiles::profile_data($name))
		// {
		// 	return false;
		// }

		// if($name == 'displayname')
		// {
		// 	$displayname = \lib\db\users::set_displayname($user_id, $value);
		// 	if(!$displayname)
		// 	{
		// 		\lib\debug::error(T_("We couldn't update your display name"));
		// 	}
		// }
		// elseif($name == 'email')
		// {
		// 	$email = \lib\db\users::set_email($user_id, $value);
		// 	if(!$email)
		// 	{
		// 		\lib\debug::error(T_("We couldn't update your email address"));
		// 	}
		// }
		// elseif($name == 'mobile')
		// {
		// 	$old_mobile = \lib\db\users::get_mobile($user_id);
		// 	if($old_mobile == null || substr($old_mobile, 0, 5) == 'temp_')
		// 	{
		// 		$mobile = \lib\db\users::set_mobile($user_id, $value);
		// 		if(!$mobile)
		// 		{
		// 			\lib\debug::error(T_("We couldn't update your mobile number"));
		// 		}
		// 	}
		// }
		// else
		// {
		// 	$profiles = \lib\utility\profiles::set_profile_data($user_id, [$name => $value]);
		// 	if(!$profiles)
		// 	{
		// 		\lib\debug::error(T_("We couldn't update your profile"));
		// 	}
		// }

		// if(\lib\debug::$status)
		// {
		// 	\lib\debug::true(T_("Profile information updated"));
		// }
	}
}
?>