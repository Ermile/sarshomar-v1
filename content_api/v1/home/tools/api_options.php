<?php
namespace content_api\v1\home\tools;
use \lib\permission;
use \lib\utility;
use \lib\debug;

trait api_options
{
	/**
	 * check permission
	 *
	 * @param      <type>  $_content     The content
	 * @param      <type>  $_permission  The permission
	 * @param      <type>  $_actions     The actions
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function check_api_permission()
	{
		return \lib\permission::access(...func_get_args());
	}


	/**
	 * set permission of this user
	 */
	public function set_api_permission($_user_id)
	{
		// if($_user_id)
		// {
		// 	$permission = [];

		// 	permission::$get_from_session = false;

		// 	$user_perm = \lib\utility\users::get_user_permission($_user_id);

		// 	if(isset($user_perm['user_permission']))
		// 	{
		// 		$permission['user']['permission']   = $user_perm['user_permission'];

		// 		if(is_numeric($user_perm['user_permission']))
		// 		{
		// 			$permission['permission'] = $this->setPermissionSession($user_perm['user_permission'], true);
		// 		}
		// 		permission::$PERMISSION       = $permission;
		// 	}
		// }
	}
}


?>