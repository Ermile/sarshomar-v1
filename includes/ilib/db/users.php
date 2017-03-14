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
		switch ($_type)
		{
			case 'all':
				$query = "SELECT COUNT(users.id) AS `count` FROM users WHERE user_port  != 'site_guest' ";
				return \lib\db::get($query, 'count', true);
				break;

			case 'available':
				$query = "SELECT COUNT(users.id) AS `count` FROM users WHERE user_port NOT LIKE '%guest%' ";
				return \lib\db::get($query, 'count', true);
				break;

			default:
				return parent::get_count($_type);
				break;
		}

	}
}
?>