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

		$query =
		"
			INSERT INTO
				postfilters
			(post_id, filter_id)
			VALUES
			($values)
		";
		return \lib\db::query($query);
	}



	/**
	 * real delete record from database
	 *
	 * @param      <type>  $_where_or_id  The where or identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_poll_id, $_filter_id)
	{
		$query = " DELETE FROM	postfilters	WHERE post_id = $_poll_id AND filter_id = $_filter_id";
		return \lib\db::query($query);
	}
}
?>