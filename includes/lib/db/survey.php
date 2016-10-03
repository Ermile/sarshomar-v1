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
	public static function set($_poll_id)
	{
		$update_post =
		[
			'post_type' => 'survey_private'
		];
		$update_post = \lib\db\posts::update($update_post, $_poll_id);


		var_dump($_poll_id);
		exit();

	}
}
?>