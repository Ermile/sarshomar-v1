<?php
namespace content_api\home;
use \lib\utility\permission;

class model extends \mvc\model
{
	/**
	 * set permission of this user
	 */
	public function permission()
	{
		$user_perm = \lib\db\users::get_user_data($this->user_id, 'user_permission');
		if(isset($user_perm['user_permission']) && is_numeric($user_perm['user_permission']))
		{
			$permission                   = $this->setPermissionSession($user_perm['user_permission'], false);
			$args                         = [];
			$args['user']['permission']   = $user_perm['user_permission'];
			$args['permission']           = $permission;
			permission::$get_from_session = false;
			permission::$PERMISSION       = $args;
		}
	}
	use tools\get_token;
	use tools\ready;
}
?>