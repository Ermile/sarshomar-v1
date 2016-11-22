<?php
namespace lib\utility;

/**
 * Class for synchronize.
 */
class sync
{

	private static $new_user_id;
	private static $old_user_id;
	/**
	 * get the mobile of web service and the telegram id
	 * and sync
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 */
	public static function web_telegram($_web_mobile, $_telegram_id)
	{
		$web = \lib\db\users::get_by_mobile(\lib\utility\filter::mobile($_web_mobile));
		if(!$web || !isset($web['id']))
		{
			return false;
		}

		$web_id = $web['id'];

		self::$new_user_id = $web_id;
		self::$old_user_id = $_telegram_id;

		\lib\db::transaction();

		self::sync_posts();
		self::sync_comments();
		self::sync_commentdetails();
		self::sync_notifications();
		self::sync_options();

		\lib\db::rollback();


	}


	/**
	 * sync all post the user has created it
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_posts()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query = "UPDATE posts SET posts.user_id = $new_user_id WHERE posts.user_id = $old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all comments the user has created it
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_comments()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query = "UPDATE comments SET comments.user_id = $new_user_id WHERE comments.user_id = $old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all commentdetails
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_commentdetails()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query = "UPDATE commentdetails SET commentdetails.user_id = $new_user_id WHERE commentdetails.user_id = $old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all notifications
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_notifications()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query = "UPDATE notifications SET notifications.user_id = $new_user_id WHERE notifications.user_id = $old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync the options table
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 */
	private static function sync_options()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		// update default record
		$query =
		"
			UPDATE
				options
			SET
				options.user_id = $new_user_id,
				options.option_value = IF(options.option_value = options.user_id, $new_user_id, options.option_value),
			WHERE
				options.user_id = $old_user_id AND
				options.option_key NOT LIKE 'user%'
		";
		\lib\db::query($query);

		// update record similar user_detail && user_dashboard && ...
		// sample value is: my_poll, my_poll_answered, my_poll_skipped, ...
		$where = ['user_id' => $old_user_id, 'option_cat' => 'user%'];
		$list = \lib\db\options::get($where);

		$set = ['option_status' => 'disable'];
		\lib\db\options::update_on_error($set, $where);

		// process dashboard data again
		$user_post = \lib\db\polls::search(null, ['user_id' => $new_user_id]);
		if($user_post && is_array($user_post))
		{
			$user_post_id = array_column($user_post, 'id');

			// poll answered
			\lib\utility\profiles::set_dashboard_data($new_user_id, 'poll_answered',
					\lib\db\polldetails::user_total_answered($new_user_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'poll_skipped',
					\lib\db\polldetails::user_total_skipped($new_user_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'survey_answered',
					\lib\db\polldetails::user_total_answered($new_user_id, ['gender' => 'survey']));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'survey_skipped',
					\lib\db\polldetails::user_total_skipped($new_user_id, ['gender' => 'survey']));

			// get count of survey the user creat it
			$user_survey = array_column($user_post, 'post_gender', 'id');
			$user_survey = array_count_values($user_survey);
			$count_user_survey = 0;
			if(isset($user_survey['survey']))
			{
				$count_user_survey = $user_survey['survey'];
			}
			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey', $count_user_survey);

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll', count($user_post_id));


			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll_answered',
					\lib\db\polldetails::people_answered($user_post_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll_skipped',
					\lib\db\polldetails::people_answered($user_post_id));


			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey_answered',
					\lib\db\polldetails::people_answered($user_post_id, ['gender' => 'survey']));


			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey_skipped',
					\lib\db\polldetails::people_answered($user_post_id, ['gender' => 'survey']));


			$comment_count_query = "SELECT COUNT(id) AS 'count' FROM comments WHERE user_id = $new_user_id";
			$comment_count = \lib\db::get($comment_count_query, 'count', true);

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'comment_count', $comment_count);
		}
	}


	/**
	 * update polldetails
	 */
	private static function sync_polldetails()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query ="SELECT * FROM polldetails WHERE polldetails.user_id = $old_user_id ";

	}
}
?>