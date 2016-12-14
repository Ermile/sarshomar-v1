<?php
namespace lib\db;

/** units managing **/
class units
{

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
			'user_id'    => $_user_id,
			'option_cat' => "user_detail_". $_user_id,
			'option_key' => "unit"
		];
		$user_unit = \lib\db\options::get($where);
		if($user_unit && isset($user_unit[0]['value']))
		{
			return $user_unit[0]['value'];
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
		$check_exist = self::user_unit($_user_id);
		if(empty($check_exist))
		{
			$arg =
			[
				'user_id'      => $_user_id,
				'option_cat'   => "user_detail_". $_user_id,
				'option_key'   => "unit",
				'option_value' => $_unit
			];
			$set_unit = \lib\db\options::insert($arg);
			return $set_unit;
		}
		return $check_exist;
	}
}
?>