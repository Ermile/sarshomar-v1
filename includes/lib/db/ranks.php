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
		'member'     => [	5			, true	],
		'filter'     => [	2			, true	],
		'report'     => [	3			, false	],
		'vot'        => [	4			, true	],
		'like'       => [	5			, true	],
		'faiv'       => [	6			, true	],
		'skip'       => [	7			, false	],
		'comment'    => [	8			, true	],
		'view'       => [	9			, true	],
		'other'      => [	10			, true	],
		'sarshomar'  => [	100000		, true	],
		'createdate' => [	12			, true	],
		'ago'        => [	13			, true 	],
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
			$post_rank['createdate'] = time();
			$post_rank['value']      = 0;
		}

		$sum = 0;
		if(isset($post_rank['value']))
		{
			$sum = $post_rank['value'];
		}

		foreach ($post_rank as $key => $value)
		{
			switch ($key)
			{
				case 'createdate':
				case 'ago':
					continue;
					break;

				default:
					if($key == $_field)
					{
						$value += $_plus;
					}

					if(array_key_exists($key, self::$values))
					{
						if(self::$values[$key][1] === true)
						{
							$sum += intval($value) * intval(self::$values[$key][0]);
						}
						elseif(self::$values[$key][1] === false)
						{
							$sum -= intval($value) * intval(self::$values[$key][0]);
						}
					}
					break;
			}
		}

		if(intval($sum) < 0)
		{
			$sum = 0;
		}

		$query  = "UPDATE ranks SET ranks.value = $sum WHERE post_id = $_poll_id";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>