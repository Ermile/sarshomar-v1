<?php
namespace lib\utility;

/** postfilters managing **/
class postfilters
{
	private static function check($_filters)
	{
			// remove full insert filter
		// for example the user set male and female filter
		// we remove the gender filter

		$sum_money_filter = 0;
		$support_filter   = \lib\db\filters::support_filter();
		$filters = [];
		foreach ($_filters as $key => $value)
		{
			if(\lib\db\filters::support_filter($key, $value))
			{
				$filters[$key] = $value;
				if($value == $support_filter[$key])
				{
					// unset($_filters[$key]);
				}
				else
				{
					$sum_money_filter += (int) \lib\db\filters::money_filter($key);
				}
			}
		}
		return $filters;
	}


	/**
	 * insert new tag in filters table
	 * @param array $_filters fields data
	 * @return mysql result
	 */
	public static function update($_filters, $_poll_id)
	{
		if(!is_array($_filters) || !$_poll_id)
		{
			return false;
		}

		$_filters = self::check($_filters);

		$saved_filters = self::get_filter($_poll_id);

		$caller_term_id = [];

		foreach ($_filters as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $index => $filter)
				{
					if(is_string($filter))
					{
						if(!isset($saved_filters[$key][$filter]))
						{
							$caller_term_id["$key:$filter"] = \lib\utility\profiles::insert_terms($key, $filter);
						}
					}
				}
			}
			elseif(is_string($value))
			{
				if(!isset($saved_filters[$key][$value]))
				{
					$caller_term_id["$key:$value"] = \lib\utility\profiles::insert_terms($key, $value);
				}
			}
		}

		$must_remove = [];
		foreach ($saved_filters as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $index => $filter)
				{
					if(is_string($filter))
					{
						if(isset($_filters[$key]))
						{
							if(!in_array($filter, $_filters[$key]))
							{
								$must_remove[] = "$key:$filter";
							}
						}
						else
						{
							$must_remove[] = "$key:$filter";
						}
					}
				}
			}
			elseif(is_string($value))
			{
				if(isset($_filters[$key]) && !in_array($value, $_filters[$key]))
				{
					$must_remove[] = "$key:$value";
				}
			}
		}
		// var_dump($must_remove, $caller_term_id);
		// var_dump($_filters, $saved_filters);
		// exit();

		if(!empty($must_remove))
		{
			$must_remove = implode("', '", $must_remove);
			$must_remove = "SELECT terms.id  AS `id` FROM terms WHERE terms.term_caller IN ('$must_remove')";
			$must_remove = \lib\db::get($must_remove, 'id');
			if(!empty($must_remove))
			{
				$must_remove = implode(",", $must_remove);
				$query =
				"
					DELETE FROM termusages
					WHERE
						termusage_foreign = 'posts' AND
						termusage_id = $_poll_id AND
						term_id IN ($must_remove);
				";
				\lib\db::query($query);
			}
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

		if(!empty($termusage_insert))
		{
			\lib\db\termusages::insert_multi($termusage_insert);
		}

		return true;
	}


	/**
	 * get the postfilters
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_filter($_poll_id)
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
}
?>