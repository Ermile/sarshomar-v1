<?php
namespace lib\db;

/** units managing **/
class units
{
	/**
	 * { function_description }
	 *
	 * @param      <type>  $_id    The identifier
	 */
	public static function get($_id = null)
	{
		$id = null;
		if($_id)
		{
			$id = " WHERE id = $_id ";
		}
		$query = "SELECT * FROM units $id";
		if($id)
		{
			$result = \lib\db::get($query, null, true);
		}
		else
		{
			$result = \lib\db::get($query);
		}
		return $result;
	}

	/**
	 * Gets the unit identifier.
	 *
	 * @param      <type>   $_unit_title  The unit title
	 *
	 * @return     boolean  The identifier.
	 */
	public static function get_id($_unit_title)
	{
		$query = "SELECT id FROM units WHERE units.title = '$_unit_title' LIMIT 1";
		$result = \lib\db::get($query, 'id', true);
		if(!$result || empty($result) || is_array($result))
		{
			return false;
		}
		return $result;
	}


	/**
	 * get the user unit
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public static function user_unit($_user_id)
	{
		$where =
		[
			'user_id'       => $_user_id,
			'option_cat'    => "user_detail_". $_user_id,
			'option_key'    => 'unit',
			'option_status' => 'enable',
			'limit'         => 1,
		];
		$user_unit = \lib\db\options::get($where);
		if($user_unit && isset($user_unit['value']))
		{
			return $user_unit['value'];
		}
		return false;
	}


	/**
	 * Sets the user unit.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_unit     The unit
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set_user_unit($_user_id, $_unit)
	{
		$result = false;
		$disable_old_unit =
		[
			'user_id'       => $_user_id,
			'option_cat'    => "user_detail_". $_user_id,
			'option_key'    => 'unit',
		];
		\lib\db\options::update_on_error(['option_status' => 'disable'], $disable_old_unit);

		$where =
		[
			'user_id'       => $_user_id,
			'option_cat'    => "user_detail_". $_user_id,
			'option_key'    => 'unit',
			'option_value'  => $_unit,
			'limit'         => 1,
		];

		$current_unit = \lib\db\options::get($where);
		unset($where['limit']);

		if(empty($current_unit))
		{
			$result = \lib\db\options::insert($where);
		}
		else
		{
			$args = $where;
			$args['option_status'] = 'enable';
			$result = \lib\db\options::update_on_error($args, $where);
		}
		return $result;
	}


	/**
	 * insert new record of units
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$default_args =
		[
			'title' => null,
			'desc'  => null,
			'meta'  => null,
		];
		$_args = array_merge($default_args, $_args);

		$set = [];
		foreach ($_args as $field => $value)
		{
			if($value === null)
			{
				$set[] = " units.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " units.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " units.$field = '$value' ";
			}
		}
		$set = implode(",", $set);

		$query =
		"
			INSERT INTO
				units
			SET
				$set
		";
		$result = \lib\db::query($query);
		return $result;
	}
/**
	 * update record of units
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_id)
	{
		$default_args =
		[
			'title' => null,
			'desc'  => null,
			'meta'  => null,
		];
		$_args = array_merge($default_args, $_args);

		$set = [];
		foreach ($_args as $field => $value)
		{
			if($value === null)
			{
				$set[] = " units.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " units.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " units.$field = '$value' ";
			}
		}
		$set = implode(",", $set);

		$query =
		"
			UPDATE
				units
			SET
				$set
			WHERE
				units.id = $_id
		";

		$result = \lib\db::query($query);
		return $result;
	}

}
?>