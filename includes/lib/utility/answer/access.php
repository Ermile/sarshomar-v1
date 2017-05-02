<?php
namespace lib\utility\answer;
use \lib\db;
use \lib\debug;
use \lib\utility;
use \lib\db\ranks;
use \lib\db\options;
use \lib\utility\users;
use \lib\utility\profiles;
use \lib\utility\shortURL;
use \lib\utility\stat_polls;

trait access
{


	/**
	 * update count in updat_every_time
	 *
	 * @var        integer
	 */
	public static $update_answer_count  = 3;


	/**
	 * update old answer where saved less than 1 hour
	 *
	 * @var        <type>
	 */
	public static $update_old_answer_in = (60 * 60 * 1);
	// public static $update_old_answer_in = (1);


	/**
	 * check access to answer to this poll
	 *
	 * @param      array   $_args  The arguments
	 */
	public static function check_time_count($_args = [])
	{

		$default_args =
		[
			'update_every_time' => self::$update_old_answer_in,
			'count'             => self::$update_answer_count,
			'time'              => (60 * 60 * 24 * 365 * 10),
			'user_id'           => null,
			'poll_id'           => null,
			'debug'             => true,
			'user_id'           => null,

		];

		if(defined('Tld') && Tld === 'dev')
		{
			$default_args['time']  = 60 * 60 * 24 * 365; // 1 year
			$default_args['count'] = 3 * 100; // 300 times
		}

		$_args = array_merge($default_args, $_args);

		// get the old answered user to this poll
		self::$old_answer = self::is_answered($_args['user_id'], $_args['poll_id']);

		if(!self::$old_answer || !is_array(self::$old_answer))
		{
			return true;
		}

		$log_meta =
		[
			'meta' =>
			[
				'old_answer' => self::$old_answer,
				'input'      => $_args,
			]
		];

		$log_search =
		[
			'user_id'        => $_args['user_id'],
			'log_desc'       => $_args['poll_id'],
			'log_status'     => 'enable',
			'caller'         => 'user:answer:update',
			'log_createdate' => ['>', "'". date("Y-m-d H:i:s", (time() - $_args['update_every_time'])). "'"],
			'get_count'      => true,
		];

		$get_log = \lib\db\logs::search(null, $log_search);
		if($get_log && is_numeric($get_log))
		{
			if(intval($get_log) >= intval($_args['count']))
			{
				\lib\db\logs::set('user:answer:error:many_update', $_args['user_id'], $log_meta);
				debug::error(T_("You have updated your answer many times and can not update it anymore"),'answer', 'permission');
				return false;
			}
		}

		// $insert_time = date("Y-m-d H:i:s");
		// $now         = time();
		// foreach (self::$old_answer as $key => $value)
		// {
		// 	if(isset($value['createdate']))
		// 	{
		// 		$insert_time = $value['createdate'];
		// 		break;
		// 	}
		// }


		// $insert_time  = strtotime($insert_time);
		// $diff_seconds = $now - $insert_time;

		// if($diff_seconds > $_args['time'])
		// {
		// 	if($_args['debug'])
		// 	{
		// 		\lib\db\logs::set('user:answer:error:max_time', $_args['user_id'], $log_meta);
		// 		debug::error(T_("Many time left, can not update or delete answer"), 'answer', 'permission');
		// 	}
		// 	return false;
		// }


		return true;
	}


	/**
	 * check access to answer or no
	 *
	 * @param      array   $_args  The arguments
	 * @param      <type>  $_type  The type
	 */
	public static function access_answer($_args = [], $_type)
	{

		if(!is_array($_args))
		{
			$_args = [];
		}

		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'debug'	  => true,
			'user_id' => null,
		];
		$_args = array_merge($default_args, $_args);

		$log_meta =
		[
			'meta' =>
			[
				'input' => $_args,
			]
		];

		switch ($_type)
		{
			case 'add':
				if(self::is_answered($_args['user_id'], $_args['poll_id']))
				{
					if($_args['debug'])
					{
						\lib\db\logs::set('user:answer:error:already_answer', $_args['user_id'], $log_meta);
						debug::error(T_("You have already answered to this poll"), 'answer', 'permission');
					}
					return false;
				}
				break;

			case 'edit':
			case 'delete':
				return self::check_time_count($_args);
				break;

			case 'check':
				$avalible = [];

				if(!self::is_answered($_args['user_id'], $_args['poll_id']))
				{
					array_push($avalible, 'add');

					if((int) \lib\db\polls::get_user_ask_me_on($_args['user_id']) === (int) $_args['poll_id'])
					{
						array_push($avalible, 'skip');
					}
				}
				else
				{
					if(self::check_time_count($_args))
					{
						array_push($avalible, 'edit');
						array_push($avalible, 'delete');
					}
				}

				return $avalible;

				break;

			default:
				\lib\db\logs::set('system:answer:error:invalid_type', $_args['user_id'], $log_meta);
				debug::error(T_("Invalid type"), 'db', 'system');
				return false;
				break;
		}
		return true;

	}
}
?>
