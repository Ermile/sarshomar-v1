<?php
namespace lib\db;

/** pollopts managing **/
class pollopts
{
	/**
	 * this library work with pollopts table
	 * v1.0
	 */


	private static $public_fields = 
	"
		`post_id`			AS `poll`,
		`key` 				AS `key`,
		`text` 				AS `text`,
		`subtype` 			AS `subtype`,
		`true` 				AS `true`,
		`groupscore` 		AS `group_score`,
		`score` 			AS `score`,
		`attachment_id` 	AS `attachment`,
		`attachmenttype` 	AS `attachment_type`,
		`status` 			AS `status`
	";

	/**
	 * insert new recrod in pollopts table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}

		$set = [];

		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_int($value))
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');
		$query = "INSERT INTO pollopts SET $set ";
		return \lib\db::query($query);
	}


	/**
	 * insert multi record in one query
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value)
		{
			$fields = array_merge($fields, $value);
		}
		// empty record not inserted
		if(empty($fields))
		{
			return true;
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value)
		{
			foreach ($fields as $field_name => $vain)
			{
				if(array_key_exists($field_name, $value) && !is_null($value[$field_name]))
				{
					if(is_numeric($value[$field_name]))
					{
						$values[] = $value[$field_name];
					}
					elseif(is_string($value[$field_name]))
					{
						$values[] = "'" . $value[$field_name] . "'";
					}
				}
				else
				{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values     = [];
		}

		$fields = join(array_keys($fields), "`,`");

		$values = join($together, "),(");

		// crate string query
		$query = "INSERT INTO pollopts (`$fields`) VALUES ($values) ";

		return \lib\db::query($query);

	}


	/**
	 * update record in pollopts table if we have error in insert
	 * get fields and value to update  WHERE fields = $value
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function update_on_error($_args, $_where)
	{
		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		if(!is_array($_args) || !is_array($_where))
		{
			return false;
		}

		$fields = [];
		$where  = [];
		foreach ($_args as $field => $value)
		{
			$fields[] = "$field = '$value'";
		}

		foreach ($_where as $field => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$where[] = "$field LIKE '$value'";
			}
			else
			{
				$where[] = "$field = '$value'";
			}
		}

		$set_fields = join($fields, ",");
		$where      = join($where, " AND ");

		// make update fields
		$query = "
				UPDATE
					pollopts
				SET
					$set_fields
				WHERE
					$where
				";

		return \lib\db::query($query);
	}


	/**
	 * update the opts by poll id and key
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_key      The key
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function update($_args, $_poll_id, $_key)
	{
		$set = [];
		foreach ($_args as $key => $value)
		{
			if(is_null($value))
			{
				$set[] = "`$key` = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = "`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = "`$key` = '$value' ";
			}
		}

		if(empty($set))
		{
			return false;
		}

		$set = implode(',', $set);
		$query = "UPDATE pollopts SET $set WHERE post_id = $_poll_id AND pollopts.key = $_key  LIMIT 1";
		return \lib\db::query($query);
	}


	/**
	 * delete record of pollopts
	 *
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function delete($_id)
	{
		// get id and delete it
		$query = "DELETE FROM pollopts WHERE pollopts.id = $_id ";
		return \lib\db::query($query);
	}


	/**
	 * get the enable opts of poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id, $_field = null)
	{
		$field     = self::$public_fields;
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
				pollopts
			WHERE
				pollopts.post_id = $_poll_id AND
				pollopts.status = 'enable'
			ORDER BY pollopts.key ASC
			";
		$result = \lib\db::get($query, $get_field);
		$result = \lib\utility\filter::meta_decode($result);

		$result = self::encode($result);

		return $result;
	}


	/**
	 * get opts of one poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_all($_poll_id, $_field = null)
	{
		$field     = self::$public_fields;
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

		$query = "SELECT $field FROM pollopts WHERE post_id = $_poll_id ORDER BY pollopts.key ASC ";
		$result = \lib\db::get($query, $get_field);
		$result = \lib\utility\filter::meta_decode($result);
		$result = self::encode($result);
		
		return $result;
	}


	/**
	 * Sets the status.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      string  $_status   The status
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set_status($_poll_id, $_status = 'disable')
	{
		$query = "UPDATE pollopts SET pollopts.status = '$_status' WHERE pollopts.post_id = $_poll_id";
		return \lib\db::query($query);
	}


	/**
	 * encode some fields
	 *
	 * @param      <type>  $_result  The result
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function encode($_result)
	{
		if(!is_array($_result))
		{
			return $_result;
		}
		
		foreach ($_result as $key => $value) 
		{	
			if(isset($value['id']))
			{
				$_result[$key]['id'] = \lib\utility\shortURL::encode($value['id']);
			}
			if(isset($value['poll']))
			{
				$_result[$key]['poll'] = \lib\utility\shortURL::encode($value['poll']);
			}
			if(isset($value['attachment']))
			{
				$_result[$key]['attachment'] = \lib\utility\shortURL::encode($value['attachment']);
			}
		}
		return $_result;
	}
}
?>