<?php
namespace ilib\db;

class users extends \lib\db\users
{

	/**
	 * Gets the count.
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     <type>  The count.
	 */
	public static function get_count($_type = null)
	{
		if($_type === 'all')
		{
			$query = "SELECT COUNT(users.id) AS `count` FROM users WHERE user_port != 'guest' ";
			return \lib\db::get($query, 'count', true);
		}
		else
		{
			return parent::get_count($_type);
		}
	}
}
?>