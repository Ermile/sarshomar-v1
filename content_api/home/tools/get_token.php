<?php
namespace content_api\home\tools;
use \lib\utility;
use \lib\debug;

trait get_token
{
	/**
	 * make token
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function token($_guest = false)
	{

		$authorization = utility::header("authorization") ? utility::header("authorization") : utility::header("Authorization");

		$api_key_parent = null;

		$where = ['option_value' => $authorization, 'option_status' => 'enable'];
		$get   = \lib\db\options::get($where);

		if(!$get || empty($get) || ( isset($get[0]) && !array_key_exists('parent_id', $get[0])))
		{
			return debug::error(T_("authorization faild (parent not found)"), 'authorization', 'access');
		}

		$parent_id = $get[0]['parent_id'];

		if(!is_null($parent_id))
		{
			return debug::error(T_("authorization faild (this authorization is not a api key)"), 'authorization', 'access');
		}

		if(isset($get[0]['id']))
		{
			$api_key_parent = $get[0]['id'];
		}

		$user_id = null;
		if($_guest === true)
		{
			$user_id = \lib\db\users::signup_inspection();
		}

		$date  = date("Y-m-d H:i:s");
		$key   = $user_id ? 'guest' : 'tmp_login';
		$token = "~Ermile~_!_". ($user_id ? $user_id : rand(1,1000)). $key. time(). rand(1,1000). $date;
		$token = utility::hasher($token);
		$token = md5($token);
		$args  =
		[
			'user_id'      => $user_id,
			'parent_id'    => $api_key_parent,
			'option_cat'   => 'token',
			'option_key'   => $key,
			'option_value' => $token,
			'option_meta'  => $date,
		];
		\lib\db\options::insert($args);

		return ['token' => $token];
	}

}
?>