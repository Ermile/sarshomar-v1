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
	 * return the status array
	 *
	 * @param      <type>   $_status  The status
	 * @param      boolean  $_update  The update
	 * @param      array    $_msg     The message
	 */
	private static function status($_status)
	{
		$return = new \lib\db\db_return();
		return $return->set_ok($_status);
	}

	/**
	 * get the mobile of web service and the telegram id
	 * and sync
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 */
	public static function web_telegram($_web_mobile, $_telegram_id)
	{
		// this function in dev mod... :)
		// return self::status(true)->set_error_code(3502);

		$mobile = \lib\utility\filter::mobile($_web_mobile);
		$web = \lib\db\users::get_by_mobile($mobile);
		if(!$web || empty($web))
		{
			// new signup in site
			// we set the mobile in telegram account and the sync is ok
			$temp_password = rand(1000,9999);
			$update_users =
			[
				'user_mobile' => $mobile,
				'user_pass'   => \lib\utility::hasher($temp_password)
			];
			\lib\db\users::update($update_users, $_telegram_id);
			return self::status(true)
				->set_password($temp_password)
				->set_error_code(3502)
				->set_message("You can login in sarshomar.com whit your username: mobile , and password: $temp_password");
		}

		if(!$web || !isset($web['id']))
		{
			return self::status(false)->set_error_code(3500);
		}

		$web_id = $web['id'];

		self::$new_user_id = $web_id;
		self::$old_user_id = $_telegram_id;

		if(self::$new_user_id == self::$old_user_id)
		{
			return self::status(true)->set_error_code(3501);
		}
		\lib\db::transaction();

		//----- sync the options
		self::sync_options();

		//----- sync the comments
		self::sync_comments();
		self::sync_commentdetails();

		//----- sync the notification
		self::sync_notifications();

		//----- sync the termuseages
		self::sync_termusages();

		//----- sync the polldetails
		//----- sync the pollstats
		self::sync_polldetails();

		//----- sync the posts
		self::sync_posts();

		//----- deactive telegram user
		self::sync_users();

		\lib\db::rollback();

		return self::status(true)->set_error_code(3502);
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

		$query = "UPDATE commentdetails SET user_id = $new_user_id WHERE user_id = $old_user_id";
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

		$query = "UPDATE notifications SET user_id = $new_user_id WHERE user_id = $old_user_id";
		return \lib\db::query($query);
	}


	/**
	 * update polldetails
	 */
	private static function sync_polldetails()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		// update all polldetails by old user id to new user id
		$query ="UPDATE IGNORE polldetails SET user_id = $new_user_id WHERE user_id = $old_user_id ";
		$user_old_answers = \lib\db::get($query);

		// get all record was not update this mean the record was duplicate
		// we must minus the records
		$query ="SELECT * FROM polldetails WHERE polldetails.user_id = $old_user_id ";
		$user_old_answers = \lib\db::get($query);

		foreach ($user_old_answers as $key => $value)
		{
			$answers_details =
			[
				'type'    => 'minus',
				'opt_key' => $value['opt'],
				'poll_id' => $value['post_id'],
				'user_id' => $value['user_id'],
				'profile' => $value['profile']
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
			// remove answer must be remove
			\lib\db\polldetails::remove($value['user_id'], $value['post_id'], $value['opt']);
		}
	}


	/**
	 * sync the termusages
	 */
	private static function sync_termusages()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$query =
		"
			UPDATE IGNORE
				termusages
			SET
				termusage_id = $new_user_id
			WHERE
				termusages.termusage_foreign = 'users' AND
				termusages.termusage_id = $old_user_id
		";
		\lib\db::query($query);
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

		// get the user namse and last nams
		$telegram_details =
		[
			'user_id'      => $old_user_id,
			'option_cat'   => 'telegram',
			'option_value' => 'id'
		];
		$telegram_details = \lib\db\options::get($telegram_details);
		if($telegram_details && is_array($telegram_details))
		{
			if(isset($telegram_details[0]['meta']))
			{
				$telegram_details = $telegram_details[0]['meta'];
				if(isset($telegram_details['first_name']))
				{
					\lib\utility\profiles::set_profile_data($new_user_id,
						['firstname' => $telegram_details['first_name']]);
				}
				if(isset($telegram_details['last_name']))
				{
					\lib\utility\profiles::set_profile_data($new_user_id,
						['lastname' => $telegram_details['last_name']]);
				}
			}
		}

		// process dashboard data again
		$user_post = \lib\db\polls::search(null,
			['user_id'    => $old_user_id,
			 'my_poll'    => true,
			 'pagenation' => false,
			 'limit'      => null]);

		// update default record
		$query =
		"
			UPDATE IGNORE
				options
			SET
				options.option_value = IF(options.option_value LIKE options.user_id, $new_user_id, options.option_value),
				options.user_id = $new_user_id
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

		if($user_post && is_array($user_post))
		{
			$user_post_id = array_column($user_post, 'id');

			// poll answered
			\lib\utility\profiles::set_dashboard_data($new_user_id, 'poll_answered',
					\lib\db\polldetails::user_total_answered($old_user_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'poll_skipped',
					\lib\db\polldetails::user_total_skipped($old_user_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'survey_answered',
					\lib\db\polldetails::user_total_answered($old_user_id, ['gender' => 'survey']));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'survey_skipped',
					\lib\db\polldetails::user_total_skipped($old_user_id, ['gender' => 'survey']));

			// get count of survey the user creat it
			$user_survey = array_column($user_post, 'gender', 'id');
			$user_survey = array_count_values($user_survey);

			$count_user_survey = 0;
			if(isset($user_survey['survey']))
			{
				$count_user_survey = $user_survey['survey'];
			}

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey', $count_user_survey);

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll',
					count($user_post_id) - $count_user_survey);

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll_answered',
					\lib\db\polldetails::people_answered($user_post_id));

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_poll_skipped',
					\lib\db\polldetails::people_skipped($user_post_id));


			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey_answered',
					\lib\db\polldetails::people_answered($user_post_id, ['gender' => 'survey']));


			\lib\utility\profiles::set_dashboard_data($new_user_id, 'my_survey_skipped',
					\lib\db\polldetails::people_skipped($user_post_id, ['gender' => 'survey']));


			$comment_count_query = "SELECT COUNT(id) AS 'count' FROM comments WHERE user_id = $old_user_id";
			$comment_count = \lib\db::get($comment_count_query, 'count', true);

			\lib\utility\profiles::set_dashboard_data($new_user_id, 'comment_count', $comment_count);
		}
	}


	/**
	 * deactive the old user
	 *
	 * @param      <type>  $new_user_id  The new user identifier
	 * @param      <type>  $old_user_id  The old user identifier
	 */
	private static function sync_users()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;
		$deactive_old_user = \lib\db\users::update(['user_status' => 'deactive'], $old_user_id);
	}
}
?>