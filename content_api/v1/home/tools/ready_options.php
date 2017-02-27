<?php
namespace content_api\v1\home\tools;

trait ready_options
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
	public static function permission($_content = null, $_permission = null, $_actions = null)
	{
		$permission = new \lib\utility\permission;
		return $permission->access($_content, $_permission, $_actions);
	}
}


?>