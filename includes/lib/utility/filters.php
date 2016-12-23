<?php
namespace lib\utility;

class filters
{
	use \lib\utility\money;
	/**
	 * get supoort filters
	 * this array exist in to pollstat table
	 * when inserting new value to this table check field
	 * all field of that table is here
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public static function support_filter($_check = null, $_value = null)
	{
		$support_filter =
		[
			'gender'           => ['male', 'female'],
			'marrital'         => ['single', 'married'],
			'internetusage'    => ['low', 'mid', 'high'],
			'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd', 'other'],
			'course'           => null,
			'age'              => null,
			'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
			'country'          => null,
			'province'         => null,
			'city'             => null,
			'employmentstatus' => ['employee', 'unemployed', 'retired'],
			'housestatus'      => ['owner', 'tenant', 'homeless'],
			'religion'         => null,
			'language'         => null,
			'industry'         => null
		];

		if($_check)
		{
			if($_value)
			{
				if(array_key_exists($_check, $support_filter))
				{
					if(is_array($support_filter[$_check]) && in_array($_value, $support_filter[$_check]))
					{
						return true;
					}
					elseif($support_filter[$_check] === null)
					{
						return null;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			elseif(array_key_exists($_check, $support_filter))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return $support_filter;
	}


	/**
	 * the mony filter
	 *
	 * @param      <type>  $_filter  The filter
	 */
	public static function money_filter($_filter = null)
	{
		if($_filter)
		{
			if(isset(self::$money_filter[$_filter]))
			{
				return self::$money_filter[$_filter];
			}
			else
			{
				return 0;
			}
		}
		return self::$money_filter;
	}


	/**
	 * insert new tag in filters table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function update($_args, $_poll_id)
	{
		if(empty($_args) || !is_array($_args) || !$_poll_id)
		{
			return false;
		}

		$exist_filter = self::get_poll_filter($_poll_id);

		$caller_term_id = [];
		foreach ($_args as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $index => $filter)
				{
					if(is_string($filter))
					{
						if(!isset($exist_filter[$key][$filter]))
						{
							$caller_term_id["$key:$filter"] = \lib\utility\profiles::insert_terms($key, $filter);
						}
					}
				}
			}
			elseif(is_string($value))
			{
				if(!isset($exist_filter[$key][$filter]))
				{
					$caller_term_id["$key:$filter"] = \lib\utility\profiles::insert_terms($key, $value);
				}
			}
		}

		$must_remove = [];
		foreach ($exist_filter as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $index => $filter)
				{
					if(is_string($filter))
					{
						if(isset($_args[$key]) && !in_array($filter, $_args[$key]))
						{
							$must_remove[] = "$key:$filter";
						}
					}
				}
			}
			elseif(is_string($value))
			{
				if(isset($_args[$key]) && !in_array($value, $_args[$key]))
				{
					$must_remove[] = "$key:$filter";
				}
			}
		}

		if(!empty($must_remove))
		{
			$must_remove = implode("', '", $must_remove);
			$query =
			"
				DELETE FROM termusages
				WHERE
					termusage_foreign = 'posts' AND
					termusage_id = $_poll_id AND
					term_id IN (SELECT terms.id FROM terms WHERE terms.term_caller IN ('$must_remove'));
			";
			\lib\db::query($query);
		}

		$term_ids         = array_column($caller_term_id, 'id');
		$termusage_insert = [];

		foreach ($term_ids as $key => $term_id)
		{
			$termusage_insert[] =
			[
				'term_id'           => $term_id,
				'termusage_foreign' => 'posts',
				'termusage_id'      => $_poll_id
			];
		}

		$insert = true;

		if(!empty($termusage_insert))
		{
			$insert = \lib\db\termusages::insert_multi($termusage_insert);
		}

		return $insert;
	}


	/**
	 * Gets the poll filter.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function get_poll_filter($_poll_id)
	{
		$terms   = \lib\db\terms::usage($_poll_id, ['term_caller', 'term_title'], 'posts', 'users%');
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


	/**
	 * get filter list and reutnr count number of member by this filter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function count_user($_args)
	{
		return 0;
	}


	/**
	 * Removes all poll filter.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove_all_poll_filter($_poll_id)
	{
		$remove = ['termusage_foreign' => 'posts', 'termusage_id' => $_poll_id];
		return \lib\db\termusages::remove($remove);
	}
}
?>