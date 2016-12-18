<?php
namespace lib\db;

/** userranks managing **/
class userranks
{
	/**
	 * the userranrank values
	 *
	 * @var        array
	 */
	private static $values =
	[
		// the user was reported
		'reported'       => [ false,	100			],
		// user use spam words
		'usespamword'    => [ false,	5			],
		// user change the profile
		'changeprofile'  => [ false,	5			],
		// user improve her profile
		'improveprofile' => [ true,		5			],
		// the user report a poll
		'report'         => [ true,		5			],
		// the report poll of the user is wrong
		'wrongreport'    => [ false,	5			],
		// user skip the poll
		'skip'           => [ false,	5			],
		// user forger her password
		'resetpassword'  => [ false,	5			],
		// account of user is verification (bit 0|1)
		'verification'   => [ true,		50			],
		// the user is valid user (bit 0|1)
		'validation'     => [ true,		5			],
		// the VIP users
		'vip'            => [ true,		100 * 100	],
		// hated <> vip , nobody user, not vip user
		'hated'          => [ false,	5			],
		// the other
		'other'          => [ true,		5			],

	];


	/**
	 * insert new record of userranks table
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      array   $_field    The field
	 */
	public static function set($_user_id, $_field = [])
	{
		$default_fields = self::$values;
		$default_fields = array_map(function(){ return 0; }, $default_fields);

		$_field = array_merge($default_fields, $_field);

		$set = [];
		foreach ($_field as $field => $value)
		{
			if($value === null)
			{
				$set[] = " userranks.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " userranks.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " userranks.$field = '$value' ";
			}
		}

		$set    = implode(",", $set);
		$query  = "INSERT INTO userranks SET userranks.user_id = $_user_id, $set ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get the userranks of userls
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_user_id, $_field = null)
	{
		$field     = '*';
		$get_field = null;
		if(is_array($_field))
		{
			$field     = '`'. join($_field, '`, `'). '`';
			$get_field = null;
		}
		elseif($_field && is_string($_field))
		{
			$field     = '`'. $_field. '`';
			$get_field = $_field;
		}

		$query =
		"
			SELECT
				$field
			FROM
				userranks
			WHERE
				userranks.user_id = $_user_id
			LIMIT 1
			-- userranks::get()
		";
		$result = \lib\db::get($query, $get_field, true);
		return $result;

	}


	/**
	 * plus the field of userranks
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_user_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options = ['replace' => false ];
		$_options        = array_merge($default_options, $_options);

		$replace = false;
		if($_options['replace'] === true)
		{
			$replace = true;
		}

		$user_rank = self::get($_user_id);
		if(empty($user_rank))
		{
			self::set($_user_id);
			$user_rank               = self::$values;
			$user_rank               = array_map(function(){ return 0; }, $user_rank);
			$user_rank['createdate'] = date("Y-m-d");
			$user_rank['value']      = 0;
		}

		$sum    = 0;
		$update = [];
		foreach ($user_rank as $key => $value)
		{
			if($key == $_field)
			{
				if($replace)
				{
					if($key === 'verification' || $key === 'validation')
					{
						if(intval($_plus) > 1)
						{
							$_plus = 1;
						}
					}
					$value    = intval($_plus);
					$update[] = " userranks.$key = $_plus ";
				}
				else
				{
					if($key === 'verification' || $key === 'validation')
					{
						if(intval($value) >= 1)
						{
							$value = 1;
							$update[] = " userranks.$key = 1 ";
						}
					}
					else
					{
						$value    = intval($value) + intval($_plus);
						$update[] = " userranks.$key = userranks.$key + 1 ";
					}
				}
			}

			if(array_key_exists($key, self::$values))
			{
				if(self::$values[$key][0] === true)
				{
					$sum += (intval($value) * intval(self::$values[$key][1]));
				}
				elseif(self::$values[$key][0] === false)
				{
					$sum -= (intval($value) * intval(self::$values[$key][1]));
				}
			}
		}

		$update[] = " userranks.value = $sum ";

		$update = implode(",", $update);
		$query  =
		"
			UPDATE
				userranks
			SET
				$update
			WHERE
				user_id = $_user_id
			LIMIT 1
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>