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
	public static function get($_id = null, $_get_title = false)
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

		if($_get_title)
		{
			if(isset($result['title']))
			{
				return $result['title'];
			}
			elseif(isset($result[0]['title']))
			{
				return array_column($result, 'title');
			}
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
		$user_unit = \lib\utility\users::get_unit($_user_id);
		if($user_unit)
		{
			return $user_unit;
		}

		// $where =
		// [
		// 	'user_id'       => $_user_id,
		// 	'option_cat'    => "user_detail_". $_user_id,
		// 	'option_key'    => 'unit',
		// 	'option_status' => 'enable',
		// 	'limit'         => 1,
		// ];
		// $user_unit = \lib\db\options::get($where);
		// if($user_unit && isset($user_unit['value']))
		// {
		// 	return $user_unit['value'];
		// }
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
		if(!$_unit)
		{
			return false;
		}

		$old_user_unit = \lib\utility\users::get_unit($_user_id);
		\lib\utility\users::set_unit($_user_id, $_unit);

		if(!$old_user_unit && is_string($_unit))
		{
			$sarshomar_budget = \lib\db\transactions::budget($_user_id, ['unit' => 1]);
			if(!empty($sarshomar_budget) && is_array($sarshomar_budget))
			{
				foreach ($sarshomar_budget as $key => $value)
				{
					\lib\db::transaction();
					$insert_id = \lib\db\transactions::set("$key:change:minus:sarshomar", $_user_id, ['minus' => $value]);
					$change_to_new_unit_value = \lib\db\exchangerates::change_unit_to('sarshomar', $_unit, $value);
					\lib\db\transactions::set("$key:change:plus:$_unit", $_user_id, ['plus' => $change_to_new_unit_value, 'parent_id' => $insert_id]);
					if(\lib\debug::$status)
					{
						\lib\db::commit();
					}
					else
					{
						\lib\db::rollback();
					}
				}
			}
		}

		return true;
		// $result = false;
		// $disable_old_unit =
		// [
		// 	'user_id'       => $_user_id,
		// 	'option_cat'    => "user_detail_". $_user_id,
		// 	'option_key'    => 'unit',
		// ];
		// \lib\db\options::update_on_error(['option_status' => 'disable'], $disable_old_unit);

		// $where =
		// [
		// 	'user_id'       => $_user_id,
		// 	'option_cat'    => "user_detail_". $_user_id,
		// 	'option_key'    => 'unit',
		// 	'option_value'  => $_unit,
		// 	'limit'         => 1,
		// ];

		// $current_unit = \lib\db\options::get($where);
		// unset($where['limit']);

		// if(empty($current_unit))
		// {
		// 	$result = \lib\db\options::insert($where);
		// }
		// else
		// {
		// 	$args = $where;
		// 	$args['option_status'] = 'enable';
		// 	$result = \lib\db\options::update_on_error($args, $where);
		// }
		// return $result;
	}


	/**
	 * find user unit
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function find_user_unit($_user_id, $_set_user_unit_if_find = false)
	{
		// get curretn unit
		$isset_unit = self::user_unit($_user_id);
		if($isset_unit)
		{
			return $isset_unit;
		}

		// get user language
		$user_language = \lib\utility\users::get_language($_user_id);
		if($user_language === 'fa' || $user_language === 'fa_IR')
		{
			if($_set_user_unit_if_find)
			{
				self::set_user_unit($_user_id, 'toman');
			}
			return 'toman';
		}

		// get users answer
		$query =
		"
			SELECT COUNT(answers.id) AS `count`, posts.post_language AS `lang` FROM answers
			INNER JOIN posts ON posts.id = answers.post_id
			WHERE
				answers.user_id = $_user_id
			GROUP BY lang
		";
		$result = \lib\db::get($query, ['lang', 'count']);
		$max    = null;

		if(is_array($result) && !empty($result))
		{
			// fin max answered users  to poll
			$max = array_search(max($result), $result);
		}

		if($max === 'fa')
		{
			if($_set_user_unit_if_find)
			{
				self::set_user_unit($_user_id, 'toman');
			}
			return 'toman';
		}
		return false;
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