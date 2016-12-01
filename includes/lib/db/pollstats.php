<?php
namespace lib\db;

/** pollstats managing **/
class pollstats
{
	/**
	 * this library work with pollstats
	 * v1.0
	 */


	/**
	 * get poll stat record
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function get($_poll_id, $_options = [])
	{
		$default_option =
		[
			'validation' => 'valid',
			'port'       => 'site',
			'field'      => '*'
		];
		$_options = array_merge($default_option, $_options);

		$field     = $_options['field'];
		if(is_array($field))
		{
			$field = '`'. join($field, '`, `'). '`';
		}

		// get result from pollstate table
		$query =
		"
			SELECT
				$field
			FROM
				pollstats
			WHERE
				post_id = $_poll_id AND
				port    = '$_options[port]' AND
				type    = '$_options[validation]'
			LIMIT 1
			-- stat_polls::get_result()
		";
		// get result and json decode all field of this record
		$result = \lib\db::get($query);
		$result = \lib\utility\filter::meta_decode($result, "/.*/");

		if(isset($result[0]))
		{
			$result = $result[0];
		}
		else
		{
			return null;
		}
		return $result;
	}


	/**
	 * insert new tag in pollstats table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		if(empty($_args))
		{
			return null;
		}
		$set = [];
		foreach ($_args as $key => $value) {
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');

		$query =
		"
			INSERT INTO
				pollstats
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * update field from pollstats table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		$query = [];
		foreach ($_args as $field => $value)
		{
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE pollstats
				SET $query
				WHERE pollstats.id = $_id;
				";

		return \lib\db::query($query);

	}
}
?>