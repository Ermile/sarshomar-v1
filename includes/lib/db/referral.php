<?php
namespace lib\db;

class referral
{


	/**
	 * set the referral of users
	 *
	 * @param      <type>  $_child_id   The child identifier
	 * @param      <type>  $_parent_id  The parent identifier
	 */
	public static function set($_child_id, $_parent_id)
	{
		return \lib\db\users::set_user_data($_child_id, "user_parent", $_parent_id);
	}


	/**
	 * Gets the count children.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The count children.
	 */
	public static function count_children($_user_id)
	{
		$query =
		"
			SELECT
				COUNT(users.id)   AS 'count',
				users.user_status AS 'status'
			FROM
				users
			WHERE
				users.user_parent = $_user_id
			GROUP BY
				users.user_status
		";
		$result = \lib\db::get($query, ['status', 'count']);
		return $result;
	}
}
?>