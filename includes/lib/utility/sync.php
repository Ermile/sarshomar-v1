<?php
namespace lib\utility;
use lib\db;
use lib\utility;
/**
 * Class for synchronize.
 */
class sync
{
	/**
	 * get the mobile of web service and the telegram id
	 * and sync
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 */
	public static function web_telegram($_web_mobile, $_telegram_id)
	{
		$web = users::get_by_mobile(filter::mobile($_web_mobile));
		if(!$web || !isset($web['id']))
		{
			return false;
		}

		$web_id = $web['id'];


	}


	/**
	 * sync all post the user has created it
	 *
	 * @param      <type>  $_new_user_id  The new user identifier
	 * @param      <type>  $_old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_posts($_new_user_id, $_old_user_id)
	{
		$query = "UPDATE posts SET posts.user_id = $_new_user_id WHERE posts.user_id = $_old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all comments the user has created it
	 *
	 * @param      <type>  $_new_user_id  The new user identifier
	 * @param      <type>  $_old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_comments($_new_user_id, $_old_user_id)
	{
		$query = "UPDATE comments SET comments.user_id = $_new_user_id WHERE comments.user_id = $_old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all commentdetails
	 *
	 * @param      <type>  $_new_user_id  The new user identifier
	 * @param      <type>  $_old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_commentdetails($_new_user_id, $_old_user_id)
	{
		$query = "UPDATE commentdetails SET commentdetails.user_id = $_new_user_id WHERE commentdetails.user_id = $_old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync all notifications
	 *
	 * @param      <type>  $_new_user_id  The new user identifier
	 * @param      <type>  $_old_user_id  The old user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function sync_notifications($_new_user_id, $_old_user_id)
	{
		$query = "UPDATE notifications SET notifications.user_id = $_new_user_id WHERE notifications.user_id = $_old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * sync the options table
	 *
	 * @param      <type>  $_new_user_id  The new user identifier
	 * @param      <type>  $_old_user_id  The old user identifier
	 */
	private static function sync_options($_new_user_id, $_old_user_id)
	{
		// update default record
		$query =
		"
			UPDATE
				options
			SET
				options.user_id = $_new_user_id,
				options.option_value = IF(options.option_value = options.user_id, $_new_user_id, options.option_value)
			WHERE
				options.user_id = $_old_user_id AND
				options.option_key NOT LIKE 'user%'
		";
		\lib\db::query($query);

		// update record similar user_detail && user_dashboard && ...
		// sample value is: my_poll, my_poll_answered, my_poll_skipped, ...
		$where = ['user_id' => $_old_user_id, 'option_cat' => 'user%'];
		$list = options::get($where);

		$set = ['option_status' => 'disable'];
		options::update_on_error($set, $where);

		// process dashboard data again
		$user_post = polls::search(null, ['user_id' => $_new_user_id]);
		if($user_post && is_array($user_post))
		{
			$user_post_id = array_column($user_post_id, 'id');

			profiles::set_dashboard_data($_new_user_id, 'poll_answered', polldetails::user_total_answered($_new_user_id));
			profiles::set_dashboard_data($_new_user_id, 'poll_skipped', polldetails::user_total_skipped($_new_user_id));
			profiles::set_dashboard_data($_new_user_id, 'my_poll', count($user_post_id));
			$user_survey = array_column($user_post, 'post_gender', 'id');

			// survey_answered
			// survey_skipped
			profiles::set_dashboard_data($_new_user_id, 'my_poll_answered', polldetails::people_answered($user_post_id));
			profiles::set_dashboard_data($_new_user_id, 'my_poll_skipped', polldetails::people_answered($user_post_id));
			// my_survey_answered
			// my_survey_skipped

			// 'my_poll'            => 0,
			// 'my_survey'          => 0,

			// 'user_referred'      => 0,
			// 'user_verified'      => 0,
			// 'comment_count'      => 0,
		}

	}
}
?>