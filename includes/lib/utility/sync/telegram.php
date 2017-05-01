<?php
namespace lib\utility\sync;

trait telegram
{

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


	private static function sync_msg($_lang = null)
	{

		$en_msg = "Sync Complete ;)";
		$en_msg .= "\n";
		$en_msg .= "🎁 Sarshomar's gift belongs to you for synced your account.";
		$en_msg .= "\n";
		$en_msg .= "Thank you";

		$fa_msg = "ورود شما به جامعه سرشمار را تبریک می‌گوییم🌹";
		$fa_msg .= "\n";
		$fa_msg .= "شما در قرعه‌کشی جشن بزرگ نیمه شعبان شرکت داده می‌شوید;)";
		$fa_msg .= "\n\n";
		$fa_msg .= "🎁 هم‌چنین ۱۰ هزار تومان اعتبار هدیه برای استفاده از سرشمار در حساب‌کاربری شما شارژ شد";

		if($_lang === 'fa_IR' || $_lang === 'fa')
		{
			return $fa_msg;
		}
		else
		{
			return $en_msg;
		}
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
			// we set the mobile in telegram account and the sync is oK
			$update_users =
			[
				'user_mobile' => $mobile,
				'user_pass'   => null,
			];

			\lib\db\users::update($update_users, $_telegram_id);

			$user_language = \lib\utility\users::get_language($_telegram_id);
			$verify =
			[
				'mobile'   => self::$mobile,
				'ref'      => null,
				'type'     => null,
				'port'     => 'site',
				'subport'  => null,
				'user_id'  => $_telegram_id,
				'language' => $user_language,
			];
			\lib\utility\users::verify($verify);

			return
			[
				'message' => self::sync_msg($user_language),
			];
		}

		if(!$web || !isset($web['id']))
		{
			\lib\db\logs::set('user:telegram:sync:error:mobile:data',$_telegram_id, ['data' => $_web_mobile]);
			return
			[
				'message' => T_("can not get mobile data")
			];
		}

		$web_id = $web['id'];

		self::$new_user_id = $web_id;
		self::$old_user_id = $_telegram_id;

		if(self::$new_user_id == self::$old_user_id)
		{
			\lib\db\logs::set('user:telegram:sync:synced',$_telegram_id, ['data' => $web_id]);
			return
			[
				'message' => T_("This account was already synced")
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
		//----- sync the answerdetails
		//----- sync the pollstats
		self::sync_answerdetails();
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
			\lib\db::commit();
			$user_language = \lib\utility\users::get_language(self::$new_user_id);
			$verify =
			[
				'mobile'   => self::$mobile,
				'ref'      => null,
				'type'     => null,
				'port'     => 'site',
				'subport'  => null,
				'user_id'  => self::$new_user_id,
				'language' => $user_language,
			];
			\lib\utility\users::verify($verify);

			\lib\db\logs::set('user:telegram:sync:successfuly',$_telegram_id, ['data' => $web_id]);

			return
			[
				'message' => self::sync_msg($user_language),
				'user_id' => $web_id
			];
		}
	}

}
?>