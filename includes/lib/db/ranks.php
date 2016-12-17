<?php
namespace lib\db;

/** ranks managing **/
class ranks
{
	/**
	 * the rank values
	 *
	 * @var        array
	 */
	private static $values =
	[
		'member'    => [ true	, 	5	 		],
		'filter'    => [ true	, 	2	 		],
		'report'    => [ false	, 	10	 		],
		'vot'       => [ true	, 	4	 		],
		'like'      => [ true	, 	5	 		],
		'faiv'      => [ true	, 	6	 		],
		'skip'      => [ false	, 	1	 		],
		'comment'   => [ true	, 	8	 		],
		'view'      => [ true	, 	1	 		],
		'other'     => [ true	, 	10	 		],
		'sarshomar' => [ true	, 	100 * 1000	],
		'ago'       => [ true	, 	3	  		],
		'vip'       => [ true 	, 	4			],
		'public'    => [ true 	, 	4			],
		'admin'     => [ true 	, 	4			],
		'money'     => [ true 	, 	4			],
		'ad'        => [ true 	, 	4			],
	];


	/**
	 * insert new record of ranks table
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      array   $_field    The field
	 */
	public static function set($_poll_id, $_field = [])
	{
		$default_fields =
		[
			'member'     => 0,
			'filter'     => 0,
			'report'     => 0,
			'vot'        => 0,
			'like'       => 0,
			'faiv'       => 0,
			'skip'       => 0,
			'comment'    => 0,
			'view'       => 0,
			'other'      => 0,
			'sarshomar'  => 0,
			'createdate' => date("Y-m-d H:i:s"),
			'ago'        => 0,
			'vip'        => 0,
			'public'     => 0,
			'admin'      => 0,
			'money'      => 0,
			'ad'         => 0
		];
		$_field = array_merge($default_fields, $_field);

		$set = [];
		foreach ($_field as $field => $value)
		{
			if($value === null)
			{
				$set[] = " ranks.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " ranks.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " ranks.$field = '$value' ";
			}
		}

		$set = implode(",", $set);

		$query = "INSERT INTO ranks	SET	ranks.post_id = $_poll_id, $set ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get the ranks of pollls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id, $_field = null)
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
				ranks
			WHERE
				ranks.post_id = $_poll_id
			LIMIT 1
			-- ranks::get()
		";
		$result = \lib\db::get($query, $get_field, true);
		return $result;

	}


	/**
	 * plus the field of ranks
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_poll_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options = ['replace' => false ];
		$_options        = array_merge($default_options, $_options);

		$replace = false;
		if($_options['replace'] === true)
		{
			$replace = true;
		}

		$post_rank = self::get($_poll_id);
		if(empty($post_rank))
		{
			self::set($_poll_id);
			$post_rank               = self::$values;
			$post_rank               = array_map(function(){ return 0; }, $post_rank);
			$post_rank['createdate'] = date("Y-m-d");
			$post_rank['value']      = 0;
		}

		$sum    = 0;
		$update = [];
		foreach ($post_rank as $key => $value)
		{
			switch ($key)
			{
				case 'createdate':
					$now       = time();
					$your_date = strtotime($value);
					$datediff  = $now - $your_date;
					$ago       =  intval($datediff / (60 * 60 * 24));
					if(isset($post_rank['ago']) && $post_rank['ago'] != $ago)
					{
						$sum      += (intval($ago) * intval(self::$values['ago'][1]));
						$update[] = " ranks.ago = $ago ";
					}
					break;

				case 'public':
					if($replace)
					{
						if(intval($_plus) == 0 || intval($_plus) == 1)
						{
							$value    = intval($_plus);
							$update[] = " ranks.$key = $_plus ";
						}
					}
					else
					{
						$value = intval($value) + intval($_plus);
						if($value > 1)
						{
							$value = 1;
						}
						$update[] = " ranks.$key = $value ";
					}
					break;

				default:
					if($key == $_field)
					{
						if($replace)
						{
							$value    = intval($_plus);
							$update[] = " ranks.$key = $_plus ";
						}
						else
						{
							$value    = intval($value) + intval($_plus);
							$update[] = " ranks.$key = ranks.$key + 1 ";
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
					break;
			}
		}

		$update[] = " ranks.value = $sum ";

		$update = implode(",", $update);
		$query  =
		"
			UPDATE
				ranks
			SET
				$update
			WHERE
				post_id = $_poll_id
			LIMIT 1
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>