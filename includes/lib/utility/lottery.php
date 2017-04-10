<?php
namespace lib\utility;

class lottery
{
	private static $mobile           = true;
	private static $verify           = true;
	private static $return           = null;

	private static $result_count     = 0;
	private static $lottery_count    = 1;
	private static $lottery_selected = [];
	private static $rand_key = [];
	/**
	 * create new lottery code
	 */
	public static function run($_args)
	{
		$default_args =
		[
			'poll_id' => null,
			'type'    => 'all',
			'return'  => self::$return,
			'mobile'  => self::$mobile,
			'verify'  => self::$verify,
			'count'   => self::$lottery_count,
		];

		$_args = array_merge($default_args, $_args);

		self::$return        = $_args['return'];
		self::$mobile        = $_args['mobile'];
		self::$verify        = $_args['verify'];
		self::$lottery_count = $_args['count'] ;

		if(!$_args['poll_id'])
		{
			return false;
		}

		switch ($_args['type'])
		{
			case 'answered':
				self::answered($_args['poll_id']);
				break;

			case 'skipped':
				self::skipped($_args['poll_id']);
				break;

			case is_numeric($_args['type']):
				self::opt($_args['poll_id'], $_args['type']);
				break;

			case 'all':
			default:
				self::all($_args['poll_id']);
				break;
		}

		return self::result();
	}


	private static function result()
	{
		$result                   = [];
		$result['total']          = self::$result_count;
		$result['selected_count'] = self::$lottery_count;
		$result['selected']       = self::$rand_key;

		var_dump(self::$lottery_selected, self::$lottery_count, , );
		exit();
	}

	private static function random($_result)
	{
		if(empty($_result))
		{
			return false;
		}

		self::$result_count     = count($_result);
		self::$rand_key         = array_rand($_result, self::$lottery_count);
		if(count(self::$rand_key) > 1)
		{
			foreach (self::$rand_key as $key => $value)
			{
				array_push(self::$lottery_selected, $_result[$value]);
			}
		}
		else
		{
			self::$lottery_selected = [$_result[self::$rand_key]];
		}
	}

	/**
	 * lottery in all answered to poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	private static function answered($_poll_id)
	{
		$mobile = null;
		if(self::$mobile)
		{
			$verify = null;
			if(self::$verify)
			{
				$verify = "users.user_verify IN ('mobile','complete') ";
			}
			else
			{
				$verify = "users.user_verify IN ('mobile','complete', 'uniqueid') ";
			}

			$query =
			"SELECT
				polldetails.*,
				users.*
			FROM
				polldetails
			INNER JOIN users ON polldetails.user_id = users.id
			WHERE
				polldetails.post_id = $_poll_id AND
				polldetails.status = 'enable' AND
				polldetails.opt <> 0 AND
				polldetails.opt IS NOT NULL AND
				users.user_status = 'active' AND
				$verify
			";
		}
		else
		{
			$query =
			"SELECT
				*
			FROM
				polldetails
			INNER JOIN users ON polldetails.user_id = users.id
			WHERE
				polldetails.post_id = $_poll_id AND
				polldetails.status = 'enable' AND
				polldetails.opt <> 0 AND
				polldetails.opt IS NOT NULL AND
				users.user_status = 'active'
			";
		}
		$result = \lib\db::get($query);
		self::random($result);
	}




	/**
	 * lottery in all skipped poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	private static function skipped($_poll_id)
	{

	}


	/**
	 * lottery in all user answer to one of opt
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	private static function opt($_poll_id, $_opt)
	{
		$mobile = null;
		if(self::$mobile)
		{
			$verify = null;
			if(self::$verify)
			{
				$verify = "users.user_verify IN ('mobile','complete') ";
			}
			else
			{
				$verify = "users.user_verify IN ('mobile','complete', 'uniqueid') ";
			}

			$query =
			"SELECT
				polldetails.*,
				users.*
			FROM
				polldetails
			INNER JOIN users ON polldetails.user_id = users.id
			WHERE
				polldetails.post_id = $_poll_id AND
				polldetails.status = 'enable' AND
				polldetails.opt = $_opt AND

				users.user_status = 'active' AND
				$verify
			";
		}
		else
		{
			$query =
			"SELECT
				*
			FROM
				polldetails
			INNER JOIN users ON polldetails.user_id = users.id
			WHERE
				polldetails.post_id = $_poll_id AND
				polldetails.status = 'enable' AND
				polldetails.opt = $_opt AND
				users.user_status = 'active'
			";
		}
		$result = \lib\db::get($query);
		self::random($result);
	}


	/**
	 *	lottery in all users
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	private static function all($_poll_id)
	{

	}

}
?>