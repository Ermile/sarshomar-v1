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
		'member'     => [ true	, 	5	 		],
		'filter'     => [ true	, 	2	 		],
		'report'     => [ false	, 	10	 		],
		'vot'        => [ true	, 	4	 		],
		'like'       => [ true	, 	5	 		],
		'faiv'       => [ true	, 	6	 		],
		'skip'       => [ false	, 	1	 		],
		'comment'    => [ true	, 	8	 		],
		'view'       => [ true	, 	1	 		],
		'other'      => [ true	, 	10	 		],
		'sarshomar'  => [ true	, 	100 * 1000	],
		'ago'        => [ true	, 	3	  		],
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
			'ago'        => 0
		];
		$_field = array_merge($default_fields, $_field);

		$query =
		"
			INSERT INTO
				ranks
			SET
				ranks.post_id    = $_poll_id,
				ranks.member     = $_field[member],
				ranks.filter     = $_field[filter],
				ranks.report     = $_field[report],
				ranks.vot        = $_field[vot],
				ranks.like       = $_field[like],
				ranks.faiv       = $_field[faiv],
				ranks.skip       = $_field[skip],
				ranks.comment    = $_field[comment],
				ranks.view       = $_field[view],
				ranks.other      = $_field[other],
				ranks.sarshomar  = $_field[sarshomar],
				ranks.createdate = '$_field[createdate]',
				ranks.ago        = $_field[ago]
		";
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
	public static function get($_poll_id)
	{
		$query = "SELECT * FROM ranks WHERE post_id = $_poll_id LIMIT 1";
		return \lib\db::get($query, null, true);
	}


	/**
	 * plus the field of ranks
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_poll_id, $_field, $_plus = 1)
	{
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
						$sum       = $sum + (intval($ago) * intval(self::$values['ago'][1]));
						$update[] = " ranks.ago = $ago ";
					}
					break;

				default:
					if($key == $_field)
					{
						$value = intval($value) + intval($_plus);
						$update[] = " ranks.$key = ranks.$key + 1 ";
					}

					if(array_key_exists($key, self::$values))
					{
						if(self::$values[$key][0] === true)
						{
							$sum = $sum + (intval($value) * intval(self::$values[$key][1]));
						}
						elseif(self::$values[$key][0] === false)
						{
							$sum = $sum - (intval($value) * intval(self::$values[$key][1]));
						}
					}
					break;
			}
		}

		if(intval($sum) < 0)
		{
			$sum = 0;
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