<?php
namespace lib\db\polls;

trait sarshomar
{
	/**
	 * get the max of sarshomar post id
	 */
	public static function sarshomar_id()
	{
		$sarshomar_id = 1000000;

		$query =
		"
			SELECT
				MAX(posts.id) AS 'id'
			FROM
				posts
			WHERE
				posts.id < $sarshomar_id
		";
		$result = \lib\db::get($query, 'id', true);
		return $result;
	}
}
?>