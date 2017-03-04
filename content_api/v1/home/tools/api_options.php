<?php
namespace content_api\v1\home\tools;
use \lib\utility\permission;
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
	public static function check_api_permission($_content = null, $_permission = null, $_actions = null)
	{
		$permission = new \lib\utility\permission;
		return $permission->access($_content, $_permission, $_actions);
	}


	/**
	 * set permission of this user
	 */
	public function set_api_permission($_user_id)
	{
		if($_user_id)
		{
			$permission = [];

			permission::$get_from_session = false;

			$user_perm = \lib\db\users::get_user_data($_user_id, 'user_permission');

			if(isset($user_perm['user_permission']))
			{
				$permission['user']['permission']   = $user_perm['user_permission'];

				if(is_numeric($user_perm['user_permission']))
				{
					$permission['permission'] = $this->setPermissionSession($user_perm['user_permission'], true);
				}
				permission::$PERMISSION       = $permission;
			}
		}
	}
}


?>