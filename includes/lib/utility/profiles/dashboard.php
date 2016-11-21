<?php
namespace lib\utility\profiles;

trait dashboard
{

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
		return $dashboard;
	}


	/**
	 * Sets the dashboard data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_title    The title
	 */
	public static function set_dashboard_data($_user_id, $_title, $_counter = 1)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_value =  options.option_value + $_counter
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
				'option_value' => $_counter,
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
}
?>