<?php
namespace lib\utility\sync;

trait web_guest
{
	public static function web_guest($_new_user_id, $_old_user_id)
	{
		\lib\db\logs::set('user:web:guest:sync:start', $_new_user_id, ['data' => $_old_user_id, 'meta' => ['input' => func_get_args()]]);


		self::$new_user_id = $_new_user_id;
		self::$old_user_id = $_old_user_id;

		if(self::$new_user_id == self::$old_user_id)
		{
			\lib\db\logs::set('user:web:guest:sync:synced',$_new_user_id, ['data' => $_old_user_id]);
			return true;
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


		// chane old answer user to valid vote
		\lib\utility\answers::change_user_validation_answers($_old_user_id);

		\lib\utility\profiles::refresh_dashboard(self::$new_user_id);
		// \content\saloos_tg\sarshomar_bot\commands\handle::send_log(\lib\debug::compile());

		// check error was happend or no
		if(!\lib\debug::$status)
		{
			\lib\db::rollback();
			\lib\db\logs::set('user:web:guest:sync:error:in:sync:rollback',$_new_user_id, ['data' => $_old_user_id]);
			return false;
		}
		else
		{
			\lib\db::commit();
			\lib\db\logs::set('user:telegram:sync:successfuly',$_new_user_id, ['data' => $_old_user_id]);
			return true;
		}
	}
}
?>