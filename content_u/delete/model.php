<?php
namespace content_u\delete;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get delete data to show
	 */
	public function get_delete()
	{

	}


	/**
	 * post data and update or insert delete data
	 */
	public function post_delete()
	{
		if(!$this->login())
		{
			return false;
		}

		$why     = utility::post("why");
		$user_id = $this->login("id");

		// save why in log
		$log_title = \lib\db\logitems::get_id("delete_account");
		if(!$log_title)
		{
			$insert_log_items =
			[
				'logitem_type'     => 'users',
				'logitem_title'    => 'delete_account',
				'logitem_priority' => 'high'
			];
			$result = \lib\db\logitems::insert($insert_log_items);
			if($result)
			{
				$log_title = intval(\lib\db::insert_id(\lib\db::$link));
			}
		}
		if($log_title)
		{
			$insert_log =
			[
				'logitem_id'     => $log_title,
				'user_id'        => $user_id,
				'log_data'       => substr($why, 0, 200),
				'log_meta'       => "$why",
				'log_createdate' => date("Y-m-d H:i:s")
			];
			\lib\db\logs::insert($insert_log);
		}
		$user_data  = \lib\db\users::get($user_id);
		$meta       = isset($user_data['user_meta']) ? $user_data['user_meta'] : null;
		$old_status = isset($user_data['user_status']) ? $user_data['user_status'] : 'awaiting';
		if($meta)
		{
			$meta = json_decode($meta, true);
		}
		if(is_array($meta))
		{
			$meta = array_merge($meta, ['old_status' => $old_status]);
		}
		else
		{
			$meta = ['old_status' => $old_status];
		}
		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);

		$update_user_status = ['user_status' => 'removed', 'user_meta' => $meta];

		\lib\db\users::update($update_user_status, $user_id);

		$this->redirector()->set_url("logout")->redirect();

	}
}
?>