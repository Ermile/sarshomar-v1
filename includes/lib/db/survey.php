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


	/**
	 * Get list of poll in this survey.
	 *
	 * @param      <type>  $_survey_id  The survey identifier
	 *
	 * @return     <type>  The poll list.
	 */
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
				posts.post_survey = '$_survey_id'
			-- survey::get_poll_list()
		";
		return \lib\db::get($query);
	}


	/**
	 * check count of poll has parent is survey id
	 * if count >= 2 return true
	 *
	 * @param      <type>  $_survey_id  The survey identifier
	 */
	public static function is_survey($_survey_id)
	{
		$query =
		"
			SELECT
				COUNT(id) AS 'id'
			FROM
				posts
			WHERE
				post_type LIKE 'survey_poll_%' AND
				post_parent = $_survey_id
		";
		$result = \lib\db::get($query,'id', true);
		if(intval($result) >= 2)
		{
			return true;
		}
		return false;
	}

	/**
	 * update survey is polls::update()
	 *
	 * @param      <type>  $_args       The arguments
	 * @param      <type>  $_survey_id  The survey identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_survey_id)
	{
		return \lib\db\polls::update($_args, $_survey_id);
	}


	/**
	 * change suervey to poll
	 * remove survey record
	 * update poll type to poll mode
	 *
	 * @param      <type>  $_survey_id  The survey identifier
	 */
	public static function change_to_poll($_survey_id)
	{
		// delete the survey record
		\lib\db\posts::delete($_survey_id);
		// get the poll of this survey
		$get_poll =
		"
			SELECT
				id
			FROM
				posts
			WHERE
				post_parent = $_survey_id
			LIMIT 1
		";
		$poll_id = \lib\db::get($get_poll, 'id', true);
		// change this suervey_poll to poll
		$query =
		"
			UPDATE
				posts
			SET
				post_type   = REPLACE(post_type, 'survey_poll', 'poll_private'),
				post_parent = NULL
			WHERE
				post_parent = $_survey_id
		";
		$result = \lib\db::query($query);

		return $poll_id;
	}
}
?>