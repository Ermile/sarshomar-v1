<?php
namespace lib\utility\answer;
use \lib\db;
use \lib\debug;
use \lib\utility;
use \lib\db\ranks;
use \lib\db\options;
use \lib\utility\users;
use \lib\db\polldetails;
use \lib\utility\profiles;
use \lib\utility\shortURL;
use \lib\utility\stat_polls;

trait is_answered
{
	/**
	 * check the user answered to this poll or no
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_answered($_user_id, $_poll_id, $_options = [])
	{
		if(!$_user_id || !$_poll_id)
		{
			// debug::error(T_("User id or poll id not found"), 'is_answered', 'db');
			return false;
		}

		if(!isset(self::$IS_ANSWERED[$_user_id][$_poll_id]))
		{
			$query =
			"
				SELECT
					*
				FROM
					polldetails
				WHERE
					polldetails.user_id = $_user_id AND
					polldetails.post_id = $_poll_id
				-- to get enable at first
				ORDER BY polldetails.status ASC
				-- answers::is_answered()
			";
			$result = db::get($query, null);
			self::$IS_ANSWERED[$_user_id][$_poll_id] = $result;
		}

		$default_options =
		[
			'type' => false,
		];
		$_options = array_merge($default_options, $_options);

		if($_options['type'] === 'all')
		{
			if(isset(self::$IS_ANSWERED[$_user_id][$_poll_id]))
			{
				return self::$IS_ANSWERED[$_user_id][$_poll_id];
			}
			return false;
		}

		if(isset(self::$IS_ANSWERED[$_user_id][$_poll_id]) && is_array(self::$IS_ANSWERED[$_user_id][$_poll_id]))
		{
			$temp = [];
			foreach (self::$IS_ANSWERED[$_user_id][$_poll_id] as $key => $value)
			{
				if(isset($value['status']) && $value['status'] == 'enable')
				{
					array_push($temp, $value);
				}
			}

			if(empty($temp))
			{
				return false;
			}
			return $temp;
		}
		return false;
	}

}
?>