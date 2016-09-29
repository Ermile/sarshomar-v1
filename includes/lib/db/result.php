<?php
namespace lib\db;

class result
{
	public static function get_random_poll_id()
	{
		$query =
		"
			SELECT
				id
			FROM
				posts
			WHERE
				post_type LIKE 'poll%' AND
				post_status = 'publish'
		";

		$result = \lib\db\posts::select($query, "get");

		$get_id = array_column($result, "id");

		if(!empty($get_id))
		{
			$random_key = array_rand($get_id);
			return $get_id[$random_key];
		}
		else
		{
			return false;
		}
	}


	public static function get_random_poll_result()
	{
		$poll_result = \lib\db\stat_polls::get_result(self::get_random_poll_id());
		return $poll_result;
	}
}
?>