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

		'reported'       => [ false,	100	],
		'usespamword'    => [ false,	5	],
		'changeprofile'  => [ false,	5	],
		'improveprofile' => [ true,		5	],
		'goodreport'     => [ true,		5	],
		'wrongreport'    => [ false,	5	],
		'skip'           => [ false,	5	],
		'resetpassword'  => [ false,	5	],
		'verification'   => [ true,		50	],
		'validation'     => [ true,		5	],
		'vip'            => [ true,		5	],
		'hated'          => [ false,	5	],
		'other'          => [ true,		5	],

	];


	/**
	 * insert new record of userranks table
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      array   $_field    The field
	 */
	public static function set($_user_id, $_field = [])
	{
		$default_fields =
		[
			'reported'       => 0,
			'usespamword'    => 0,
			'changeprofile'  => 0,
			'improveprofile' => 0,
			'goodreport'     => 0,
			'wrongreport'    => 0,
			'skip'           => 0,
			'resetpassword'  => 0,
			'verification'   => 0,
			'validation'     => 0,
			'vip'            => 0,
			'hated'          => 0,
			'other'          => 0,
		];
		$_field = array_merge($default_fields, $_field);

		$query =
		"
			INSERT INTO
				userranks
			SET
				userranks.user_id 		 = $_user_id,
				userranks.reported       = $_field[reported],
				userranks.usespamword    = $_field[usespamword],
				userranks.changeprofile  = $_field[changeprofile],
				userranks.improveprofile = $_field[improveprofile],
				userranks.goodreport     = $_field[goodreport],
				userranks.wrongreport    = $_field[wrongreport],
				userranks.skip           = $_field[skip],
				userranks.resetpassword  = $_field[resetpassword],
				userranks.verification   = $_field[verification],
				userranks.validation     = $_field[validation],
				userranks.vip            = $_field[vip],
				userranks.hated          = $_field[hated],
				userranks.other          = $_field[other]

		";
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