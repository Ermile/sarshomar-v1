<?php
namespace lib\utility;
use \lib\db\filters;
use \lib\utility;
use \lib\debug;
use \lib\db;

/** postfilters managing **/
class postfilters
{
	private static function check($_filters)
	{
		$sum_money_filter = 0;
		$support_filter   = filters::support_filter();
		$filters          = [];

		foreach ($_filters as $key => $value)
		{
			if(!filters::support_filter($key, $value))
			{
				return debug::error(T_("Invalid parameter :key", ['key' => $key]),'from', 'arguments');
			}
		}
		return $_filters;
	}


	/**
	 * insert new tag in filters table
	 * @param array $_filters fields data
	 * @return mysql result
	 */
	public static function update($_filters, $_poll_id)
	{
		if(!is_array($_filters))
		{
			return debug::error(T_("Parameter filters must be array"), 'filters', 'db');
		}

		if(!$_poll_id)
		{
			return debug::error(T_("Poll id not set"), 'poll_id', 'db');
		}

		if(isset($_filters['count']))
		{
			unset($_filters['count']);
		}

		$_filters = self::check($_filters);

		if(!debug::$status)
		{
			return;
		}

		$saved_caller  = [];

		$saved_filters = self::get_filter($_poll_id, true);

		if(!empty($saved_filters))
		{
			$saved_caller = array_column($saved_filters, 'term_caller');
		}

		$must_insert = [];
		$no_change   = [];

		foreach ($_filters as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $k => $v)
				{
					$caller = self::caller_finder([$key => $v]);
					if(!in_array($caller, $saved_caller))
					{
						$must_insert[] = $caller;
					}
					else
					{
						$no_change[] = $caller;
					}
				}
			}
			else
			{
				$caller = self::caller_finder([$key => $value]);
				if(!in_array($caller, $saved_caller))
				{
					$must_insert[] = $caller;
				}
				else
				{
					$no_change[] = $caller;
				}
			}
		}

		$must_remove = [];
		foreach ($saved_caller as $key => $value)
		{
			if(!in_array($value, $must_insert) && !in_array($value, $no_change))
			{
				$must_remove[] = $value;
			}
		}

		if(!empty($must_remove))
		{
			$must_remove = implode("','", $must_remove);
			$must_remove =
			"	SELECT terms.id  AS `id`
				FROM terms
				WHERE terms.term_caller IN ('$must_remove') AND terms.term_type = 'sarshomar'
			";
			$must_remove = \lib\db::get($must_remove, 'id');
			if(!empty($must_remove))
			{
				$must_remove = implode(",", $must_remove);
				$query =
				"
					DELETE FROM termusages
					WHERE
						termusage_foreign = 'filter' AND
						termusage_id = $_poll_id AND
						term_id IN ($must_remove);
				";
				\lib\db::query($query);
			}
		}

		$termusage_insert = [];
		if(!empty($must_insert))
		{
			$must_insert = implode("','", $must_insert);
			$term_ids_query =
			"SELECT
				terms.id AS `id`
			FROM
				terms
			WHERE
				terms.term_caller IN ('$must_insert') AND
				terms.term_type = 'sarshomar'
			";
			$term_ids = db::get($term_ids_query, 'id');

			if(!empty($term_ids))
			{

				foreach ($term_ids as $key => $term_id)
				{
					$termusage_insert[] =
					[
						'term_id'           => $term_id,
						'termusage_foreign' => 'filter',
						'termusage_id'      => $_poll_id
					];
				}
			}
		}

		if(!empty($termusage_insert))
		{
			\lib\db\termusages::insert_multi($termusage_insert);
		}

		return true;
	}

	public static function caller_finder($_array)
	{
		$caller = [];
		if(is_array($_array))
		{

			foreach ($_array as $key => $value)
			{
				array_push($caller, $key);
				if(is_array($value))
				{
					array_push($caller, self::caller_finder($value));
				}
				else
				{
					array_push($caller, $value);
				}
			}
		}
		else
		{
			array_push($caller, $_array);
		}

		return implode(':', $caller);
	}
	/**
	 * get the postfilters
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_filter($_poll_id, $_raw = false)
	{
		if($_raw)
		{
			$terms   = \lib\db\terms::usage($_poll_id, [], 'filter', 'sarshomar');
			return $terms;
		}

		$terms   = \lib\db\terms::usage($_poll_id, ['term_caller', 'term_title'], 'filter', 'sarshomar');
		$poll_filter = [];
		foreach ($terms as $caller => $filter)
		{
			$explode = explode(':', $caller);
			if(isset($explode[0]) && isset($explode[1]))
			{
				$poll_filter[$explode[0]][$explode[1]] = $explode[1];
			}
		}
		return $poll_filter;
	}
}
?>