<?php
namespace ilib\db;

class users extends \lib\db\users
{

	/**
	 * Gets the by username.
	 *
	 * @param      <type>  $_username  The username
	 *
	 * @return     <type>  The by username.
	 */
	public static function get_by_username($_username)
	{
		$query = "SELECT * FROM users WHERE user_username  = '$_username' LIMIT 1 ";
		return \lib\db::get($query, null, true);
	}


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

			case 'port':
				$query = "SELECT COUNT(users.id) AS `count`, `user_port` AS `port` FROM users GROUP BY user_port ";
				$result =  \lib\db::get($query, ['port', 'count']);
				$temp = [];
				foreach ($result as $key => $value)
				{
					if(!$key)
					{
						$temp['unknown'] = (int) $value;
					}
					else
					{
						$temp[$key] = (int) $value;
					}
				}
				return $temp;
				break;
			default:
				return parent::get_count($_type);
				break;
		}

	}
}
?>