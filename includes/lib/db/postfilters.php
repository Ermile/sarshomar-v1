<?php
namespace lib\db;

/** postfilters managing **/
class postfilters
{

	/**
	 * insert new recrod in postfilters table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function set($_poll_id, $_filter_id)
	{
		$set = [];
		if(!is_array($_filter_id))
		{
			$_filter_id = [$_filter_id];
		}

		foreach ($_filter_id as $key => $value)
		{
			array_push($set, implode(",", [$_poll_id, $value]));
		}
		$values = null;
		if(!empty($set))
		{
			$values = implode("),(", $set);
		}
		else
		{
			return false;
		}

		$query = "INSERT IGNORE INTO postfilters (post_id, filter_id) VALUES ($values) ";
		return \lib\db::query($query);
	}



	/**
	 * real delete record from database
	 *
	 * @param      <type>  $_where_or_id  The where or identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_poll_id, $_filter_id = null)
	{
		$filter_id = null;
		if($_filter_id !== null)
		{
			$filter_id = " AND filter_id = $_filter_id ";
		}
		$query = " DELETE FROM	postfilters	WHERE post_id = $_poll_id $filter_id";
		return \lib\db::query($query);
	}


	/**
	 * get the postfilters
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id, $_join_by_filters = true)
	{
		if($_join_by_filters)
		{
			$query =
			"
				SELECT
					filters.*
				FROM
					postfilters
				INNER JOIN filters ON filters.id = postfilters.filter_id
				WHERE
					postfilters.post_id = $_poll_id
			";
		}
		else
		{
			$query = "SELECT * FROM postfilters WHERE post_id = $_poll_id";
		}
		return \lib\db::get($query);
	}
}
?>