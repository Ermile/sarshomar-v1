<?php
namespace lib\utility\profiles;

trait dashboard
{

	public static $dashboard_data =
	[
		'poll_answered'      => 0,
		'poll_skipped'       => 0,
		'survey_answered'    => 0,
		'survey_skipped'     => 0,
		'my_poll'            => 0,
		'my_survey'          => 0,
		'my_poll_answered'   => 0,
		'my_poll_skipped'    => 0,
		'my_survey_answered' => 0,
		'my_survey_skipped'  => 0,
		'user_referred'      => 0,
		'user_verified'      => 0,
		'comment_count'      => 0,
		'draft_count'        => 0,
		'publish_count'      => 0,
		'awaiting_count'     => 0,
	];

	public static function refresh_dashboard($_user_id)
	{
		return true;
	}

	/**
	 * Gets the dashboard data.
	 * some field in users table
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The dashboard data.
	 */
	public static function get_dashboard_data($_user_id)
	{
		$query =
		"
			SELECT
				option_key AS 'key',
				option_value AS 'value'
			FROM
				options
			WHERE
				post_id IS NULL AND
				user_id    = $_user_id AND
				option_cat = 'user_dashboard_$_user_id' AND
				option_status = 'enable'
			-- profiles::get_dashboard_data()
		";
		$dashboard = \lib\db::get($query, ['key', 'value']);
		$dashboard['user_referred'] = self::user_ref($_user_id);
		$dashboard['user_verified'] = self::user_ref($_user_id, 'active');
		return $dashboard;
	}


	/**
	 * Sets the dashboard data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_title    The title
	 */
	public static function set_dashboard_data($_user_id, $_title, $_plus = 1)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_value =  options.option_value + $_plus
			WHERE
				options.post_id IS NULL AND
				options.user_id       = $_user_id AND
				options.option_cat    = 'user_dashboard_$_user_id' AND
				options.option_key    = '$_title' AND
				options.option_status = 'enable'
			LIMIT 1
			-- profiles::set_dashboard_data()
		";
		$result = \lib\db::query($query);
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$insert_options =
			[
				'post_id'      => null,
				'option_value' => $_plus,
				'user_id'      => $_user_id,
				'option_cat'   => "user_dashboard_$_user_id",
				'option_key'   => "$_title"
			];
			\lib\db\options::insert($insert_options);
		}
	}


	/**
	 * save count of people see my poll
	 * load this data in dashboard
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function people_see_my_poll($_user_id, $_poll_id, $_title)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_value =  options.option_value + 1
			WHERE
				options.post_id    IS NULL AND
				options.user_id    = (SELECT user_id FROM posts WHERE posts.id = $_poll_id LIMIT 1)	AND
				options.option_cat = CONCAT('user_dashboard_', options.user_id) AND
				options.option_key = CONCAT('my_',  IF((SELECT IFNULL(post_survey,FALSE) FROM posts WHERE posts.id = $_poll_id LIMIT 1), 'survey','poll'), '_$_title')
				-- profiles::people_see_my_poll()
		";
		$result = \lib\db::query($query);
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$insert_options =
			"
				INSERT INTO
					options
				SET
					options.post_id      = NULL,
					options.user_id      = (SELECT user_id FROM posts WHERE posts.id = $_poll_id LIMIT 1),
					options.option_cat   = CONCAT('user_dashboard_', options.user_id),
					options.option_key   = CONCAT('my_',  IF((SELECT IFNULL(post_survey,FALSE) FROM posts WHERE posts.id = $_poll_id LIMIT 1), 'survey','poll'), '_$_title'),
					options.option_value = 1
				-- profiles::people_see_my_poll()
			";
			\lib\db::query($insert_options);
		}
	}


	/**
	 * get count of user refrred
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_type     The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function user_ref($_user_id, $_type = null)
	{
		if($_type === null)
		{
			$type = null;
		}
		else
		{
			$type = " AND users.user_status = '$_type' ";
		}

		$query =
		"
			SELECT
				COUNT(users.id) AS 'count'
			FROM
				users
			WHERE
				users.user_parent = $_user_id
				$type
		";
		return \lib\db::get($query, 'count', true);
	}
}
?>