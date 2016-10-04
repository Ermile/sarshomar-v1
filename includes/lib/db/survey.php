<?php
namespace lib\db;

class survey
{
	/**
	 * set post status to survey
	 * add new record to options table to find suervey
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function insert($_args)
	{
		return \lib\db\polls::insert($_args);
	}

	public static function get_poll_list($_survey_id)
	{
		$query =
		"
			SELECT
				posts.post_url AS 'url',
				posts.post_title AS 'title'
			FROM
				posts
			WHERE
				posts.post_parent = '$_survey_id' AND
				posts.post_type LIKE 'survey_poll_%'
		";
		return \lib\db::get($query);
	}
}
?>