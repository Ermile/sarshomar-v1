<?php
namespace lib\utility;

/**
 * Class for synchronize.
 */
class sync
{

	private static $new_user_id;
	private static $old_user_id;
	private static $mobile;
	// check error was happend
	private static $has_error = false;


	/**
	 * return status by db_return class
	 *
	 * @param      <type>  $_status  The status
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function status($_status)
	{
		$return = new \lib\db\db_return();
		return $return->set_ok($_status);
	}


	/**
	 * check synced the web and telegram or no
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 */
	public static function is_telegram_sync($_telegram_id)
	{
		$user_detail = \lib\db\users::get($_telegram_id);
		if(isset($user_detail['user_mobile']))
		{
			if(preg_match("/^tg\_\d+$/", $user_detail['user_mobile']))
			{
				return false;
			}
			elseif (preg_match("/^\d+$/", $user_detail['user_mobile']))
			{
				return true;
			}
		}
		return false;
	}


	/**
	 * get the mobile of web service and the telegram id
	 * and sync
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function web_telegram($_web_mobile, $_telegram_id)
	{
		\lib\db\logs::set('user:telegram:sync:start', null, ['data' => $_web_mobile, 'meta' => ['input' => func_get_args()]]);
		$mobile       = \lib\utility\filter::mobile($_web_mobile);
		self::$mobile = $mobile;
		$web          = \lib\db\users::get_by_mobile($mobile);
		if(!$web || empty($web))
		{
			\lib\db\logs::set('user:telegram:sync:webaccount:not:exist',$_telegram_id, ['data' => $_web_mobile]);
			// new signup in site
			// we set the mobile in telegram account and the sync is ok
			$temp_password = rand(100000,999999);
			$update_users =
			[
				'user_mobile' => $mobile,
				'user_pass'   => \lib\utility::hasher($temp_password)
			];
			\lib\db\users::update($update_users, $_telegram_id);
			return [
				'password' => $temp_password,
				'message' => T_("You can login to Sarshomar.com with your mobile", ['mobile' => $mobile]),
			];
		}

		if(!$web || !isset($web['id']))
		{
			\lib\db\logs::set('user:telegram:sync:error:mobile:data',$_telegram_id, ['data' => $_web_mobile]);
			return [
				'message' => T_("can not get mobile data")
			];
		}

		$web_id = $web['id'];

		self::$new_user_id = $web_id;
		self::$old_user_id = $_telegram_id;

		if(self::$new_user_id == self::$old_user_id)
		{
			\lib\db\logs::set('user:telegram:sync:synced',$_telegram_id, ['data' => $web_id]);
			return [
				'message' => T_("this account was already synced")
			];
		}

		// start trasaction of mysql engine
		\lib\db::transaction();
		//----- sync comments
		self::sync_comments();
		//----- sync comment dtails
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
		//----- sync the options
		self::sync_options();
		//----- deactive telegram user
		self::sync_transactions();
		//----- sync the logs table
		self::sync_logs();
		//----- sync the userranks
		self::sync_userranks();
		//----- deactive telegram user
		self::sync_users();
		\lib\utility\profiles::refresh_dashboard(self::$new_user_id);
		// \content\saloos_tg\sarshomar_bot\commands\handle::send_log(\lib\debug::compile());

		// check error was happend or no
		if(!\lib\debug::$status)
		{
			\lib\db::rollback();
			\lib\db\logs::set('user:telegram:sync:error:in:sync:rollback',$_telegram_id, ['data' => $web_id]);
			return ['message' => T_("The operation encountered an error.")];
		}
		else
		{
			\lib\db\users::update(['user_verify' => 'mobile'], self::$new_user_id);
			\lib\db::commit();
			\lib\db\logs::set('user:telegram:sync:successfuly',$_telegram_id, ['data' => $web_id]);
			return [
				'message' => T_("sync complete"),
				'user_id' => $web_id
			];
		}
	}


	/**
	 * sync all post the user has created it
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
			if(
				!isset($value['post_id']) ||
				!isset($value['user_id']) ||
				!isset($value['profile']) ||
				!isset($value['validstatus'])
			  )
			{
				continue;
			}

			$answers_details =
			[
				'type'        => 'minus',
				'update_mode' => 'delete',
				'opt_key'     => $value['opt'],
				'poll_id'     => $value['post_id'],
				'user_id'     => $value['user_id'],
				'profile'     => $value['profile'],
				'validation'  => $value['validstatus'],
				'user_verify' => $value['validstatus'],
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
			'option_value' => 'id',
			'limit' 	   => 1,
		];
		$telegram_details = \lib\db\options::get($telegram_details);
		if($telegram_details && is_array($telegram_details))
		{
			if(isset($telegram_details['meta']))
			{
				$telegram_details = $telegram_details['meta'];
				if(isset($telegram_details['first_name']))
				{
					\lib\utility\profiles::set_profile_data($new_user_id, ['firstname' => $telegram_details['first_name']]);
				}
				if(isset($telegram_details['last_name']))
				{
					\lib\utility\profiles::set_profile_data($new_user_id, ['lastname' => $telegram_details['last_name']]);
				}
			}
		}

		// update default record
		// $query =
		// "
		// 	UPDATE IGNORE
		// 		options
		// 	SET
		// 		options.option_value = IF(options.option_value LIKE options.user_id, $new_user_id, options.option_value)
		// 	WHERE
		// 		options.user_id = $old_user_id AND
		// 		options.option_key NOT LIKE 'user%'
		// ";
		// \lib\db::query($query);

		// update_user 10000134
		// user_detail 10000134
		// user_dashboard 10000134
		// update record similar user_detail && user_dashboard && ...
		// sample value is: my_poll, my_poll_answered, my_poll_skipped, ...
		$option_cats = ['user_dashboard', 'update_user', 'user_detail', 'history'];
		foreach ($option_cats as $key => $value)
		{
			$update_query ="UPDATE IGNORE options
				SET
					option_cat = '{$value}_{$new_user_id}',
					user_id = $new_user_id
				WHERE
					option_cat = '{$value}_{$old_user_id}' AND
					user_id = $old_user_id";
			\lib\db::query($update_query);
			// \lib\db::query("DELETE FROM options	WHERE option_cat = '{$value}_{$old_user_id}' AND user_id = $old_user_id ");
			\lib\db::query("UPDATE options SET option_status = 'disable' WHERE option_cat = '{$value}_{$old_user_id}' AND user_id = $old_user_id ");
		}

		\lib\db::query("UPDATE IGNORE options SET user_id = $new_user_id WHERE user_id = $old_user_id ");
		\lib\db::query("UPDATE options SET option_status = 'disable' WHERE user_id = $old_user_id ");
	}


	/**
	 * deactive the old user
	 */
	private static function sync_users()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;

		$update_new_user                     = [];
		$update_new_user['user_validstatus'] = 'valid';

		$current_status = \lib\db\users::get_user_data($new_user_id, 'user_status');
		if($current_status == 'awaiting')
		{
			$update_new_user['user_status'] = 'active';
		}

		// deactive_old_user
		\lib\db\users::update($update_new_user, $new_user_id);
		// deactive_old_user
		\lib\db\users::update(['user_status' => 'deactive'], $old_user_id);

		\lib\utility\users::verify(['user_id' => $new_user_id, 'mobile' => self::$mobile]);

	}


	/**
	 * sync the transactions
	 */
	private static function sync_transactions()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;
		$query =
		"
			INSERT INTO transactions
			(
				transactions.title,
				transactions.transactionitem_id,
				transactions.user_id,
				transactions.type,
				transactions.unit_id,
				transactions.plus,
				transactions.minus,
				transactions.budgetbefore,
				transactions.budget,
				transactions.status,
				transactions.meta,
				transactions.desc,
				transactions.related_user_id,
				transactions.parent_id,
				transactions.finished
			)
			SELECT
				transactions.title,
				transactions.transactionitem_id,
				$new_user_id,
				transactions.type,
				transactions.unit_id,
				transactions.plus,
				transactions.minus,
				transactions.budgetbefore,
				transactions.budget,
				transactions.status,
				transactions.meta,
				transactions.desc,
				transactions.related_user_id,
				transactions.parent_id,
				transactions.finished
			FROM
				transactions
			WHERE
				transactions.user_id = $old_user_id
		";
		\lib\db::query($query);
	}


	/**
	 * sync the logs
	 */
	private static function sync_logs()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;
		$query =
		"
			INSERT INTO logs
			(
				logs.logitem_id,
				logs.user_id,
				logs.log_data,
				logs.log_meta,
				logs.log_status,
				logs.log_createdate,
				logs.date_modified
			)
			SELECT
				logs.logitem_id,
				$new_user_id,
				logs.log_data,
				logs.log_meta,
				logs.log_status,
				logs.log_createdate,
				logs.date_modified
			FROM
				logs
			WHERE
				logs.user_id = $old_user_id
		";
		\lib\db::query($query);
	}


	/**
	 * sync the userranks
	 */
	private static function sync_userranks()
	{
		$new_user_id = self::$new_user_id;
		$old_user_id = self::$old_user_id;
		$old_user_rank = \lib\db\userranks::get($old_user_id);
		$new_user_rank = \lib\db\userranks::get($new_user_id);

		unset($old_user_rank['id']);
		unset($new_user_rank['id']);
		unset($old_user_rank['value']);
		unset($new_user_rank['value']);
		unset($old_user_rank['user_id']);
		unset($new_user_rank['user_id']);

		if(empty($new_user_rank))
		{
			return;
		}

		$set = [];
		foreach ($new_user_rank as $key => $value)
		{
			if(isset($old_user_rank[$key]))
			{
				$new_user_rank[$key] = (float) $old_user_rank[$key] + (float) $value;
			}

			switch ($key)
			{
				case 'verification':
				case 'validation':
					if($new_user_rank[$key] > 1)
					{
						$new_user_rank[$key] = 1;
					}
					break;
			}
			$set[] = " userranks.`$key` = ". $new_user_rank[$key];
		}
		$set = implode(" , ", $set);
		$query = "UPDATE userranks SET $set WHERE user_id = $new_user_id LIMIT 1";
		\lib\db::query($query);
		$query = "DELETE FROM userranks WHERE user_id = $old_user_id";
		\lib\db::query($query);
	}

}
?>