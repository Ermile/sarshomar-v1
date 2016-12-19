<?php
namespace lib\db;

/** socialapi managing **/
class socialapi
{

	/**
	 * return the status array
	 *
	 * @param      <type>   $_status  The status
	 * @param      boolean  $_update  The update
	 * @param      array    $_msg     The message
	 */
	public static function status($_status)
	{
		$return = new \lib\db\db_return();
		return $return->set_ok($_status);
	}


	/**
	 * insert new record of sosialapi
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function insert($_args)
	{
		if(!is_array($_args))
		{
			return self::status(false)->set_error_code(3524);
		}
		// default field
		$default_args =
		[
			'uniqueid' => null,
			'user_id'  => null,
			'type'     => null,
			'request'  => null,
			'response' => null
		];
		$_args = array_merge($default_args, $_args);

		// check require field and return syntax error if error
		if(
			$_args['uniqueid'] === null ||
			$_args['user_id']  === null ||
			$_args['type']     === null
		  )
		{
			return self::status(false)->set_error_code(3520);
		}

		// insert query
		$query =
		"
			INSERT INTO
				socialapi
			SET
				socialapi.uniqueid = '$_args[uniqueid]',
				socialapi.user_id  = $_args[user_id],
				socialapi.type     = '$_args[type]',
				socialapi.request  = '$_args[request]',
				socialapi.response = '$_args[response]'
		";
		$result = \lib\db::query($query);
		// make db return
		if(!$result)
		{
			return self::status($result)->set_mysql_error(\lib\db::error())->set_error_code(3521);
		}
		return self::status($result)->set_result($result)->set_error_code(3522);
	}


	/**
	 * get a rerord of socialapi
	 *
	 * @param      <type>  $_where  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_where)
	{
		// check syntax error
		if(!is_array($_where))
		{
			return self::status(false)->set_error_code(3524);
		}

		// default where
		$default_where =
		[
			'uniqueid' => null,
			'user_id'  => null,
			'type'     => null,
			'request'  => null,
			'response' => null
		];
		$_where = array_merge($default_where, $_where);

		// check require field and return 'where not complete' error if error
		if($_where['uniqueid'] === null || $_where['type'] === null)
		{
			return self::status(false)->set_error_code(3525);
		}

		// make where
		$where = [];
		foreach ($_where as $field => $value)
		{
			if(is_numeric($value))
			{
				$where[] = " socialapi.$field = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " socialapi.$field = '$value' ";
			}
		}
		$where = implode(" AND ", $where);

		// select query to get record
		$query = "SELECT * FROM socialapi WHERE	$where	LIMIT 1 ";

		$result = \lib\db::get($query, null, true);
		// return db return
		if($result)
		{
			return self::status(true)->set_result($result);
		}
		else
		{
			return self::status(false)->set_result($result)->set_mysql_error(\lib\db::error());
		}
	}


	/**
	 * update new record of sosialapi
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function update($_args, $_where)
	{
		// check argumens and return syntax error if error
		if(!is_array($_args) || !is_array($_where))
		{
			return self::status(false)->set_error_code(3524);
		}
		// default args
		$default_args =
		[
			'uniqueid' => null,
			'user_id'  => null,
			'type'     => null,
			'request'  => null,
			'response' => null
		];
		$_args = array_merge($default_args, $_args);
		// check require args
		if(
			$_args['uniqueid'] === null ||
			$_args['user_id']  === null ||
			$_args['type']     === null
		  )
		{
			return self::status(false)->set_error_code(3520);
		}
		// make srign of args in query
		$set = [];
		foreach ($_args as $field => $value)
		{
			if(is_numeric($value))
			{
				$set[] = " socialapi.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " socialapi.$field = '$value' ";
			}
		}

		$set = implode(" , ", $set);

		// default where
		$default_where =
		[
			'uniqueid' => null,
			'user_id'  => null,
			'type'     => null,
			'request'  => null,
			'response' => null
		];
		$_where = array_merge($default_where, $_where);
		// check require where
		if(
			$_where['uniqueid'] === null ||
			$_where['type']     === null
		  )
		{
			return self::status(false)->set_error_code(3525);
		}

		// make string where if query
		$where = [];
		foreach ($_where as $field => $value)
		{
			if(is_numeric($value))
			{
				$where[] = " socialapi.$field = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " socialapi.$field = '$value' ";
			}
		}
		$where = implode(" AND ", $where);

		// update query
		$query = "UPDATE socialapi SET $set	WHERE $where LIMIT 1 ";
		$result = \lib\db::query($query);
		// return db return
		if(!$result)
		{
			return self::status($result)
					->set_mysql_error(\lib\db::error())
					->set_error_code(3528);
		}
		return self::status($result)
					->set_result($result)
					->set_args($_args)
					->set_where($_where)
					->set_error_code(3527);
	}
}
?>