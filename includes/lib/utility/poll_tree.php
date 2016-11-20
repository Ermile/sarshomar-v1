<?php
namespace lib\utility;

class poll_tree
{
	/**
	 * Sets the poll tree.
	 *
	 * @param      <type>   $_args  The arguments
	 * get parent id, child id and options to lock child poll to opt of parent poll
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set($_args)
	{
		if(isset($_args['parent']))
		{
			$parent = $_args['parent'];
		}
		else
		{
			return false;
		}

		if(isset($_args['opt']))
		{
			$opt = $_args['opt'];
		}
		else
		{
			$opt = "opt";
		}

		if(isset($_args['child']))
		{
			$child = $_args['child'];
		}
		else
		{
			return false;
		}

		$update_poll =
		[
			'post_parent' => $parent,
		];
		$result = \lib\db\posts::update($update_poll, $child);

		$option_insert =
		[
			'post_id'      => $child,
			'option_cat'   => 'poll_'. $child,
			'option_key'   => 'tree_'. $parent,
			'option_value' => $opt
		];

		$option_result = \lib\db\options::insert($option_insert);

		if($option_result && $result)
		{
			return true;
		}
		else
		{
			if(!$option_result)
			{
				$where = $option_insert;
				$args = $option_insert;
				$args['option_status'] = 'enable';
				return \lib\db\options::update_on_error($args, $where);
			}
			return false;
		}
	}


	/**
	 * remove poll tree
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_poll_id)
	{

		$update_poll =
		[
			'post_parent' => null,
		];
		$result = \lib\db\posts::update($update_poll, $_poll_id);
		$disable_option_record =
		"
			UPDATE
				options
			SET
				option_status = 'disable'
			WHERE
				post_id = $_poll_id AND
				option_cat = 'poll_$_poll_id' AND
				option_key LIKE 'tree%'
		";
		$resutl = \lib\db::query($disable_option_record);
		return $resutl;
	}


	/**
	 * update poll tree
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_poll_id, $_args)
	{
		$remove = self::remove($_poll_id);
		if($remove)
		{
			return self::set($_args);
		}
		return false;
	}

	/**
	 * get the record of poll tree in option table
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id)
	{
		$where =
		[
			'post_id'      => $_poll_id,
			'option_cat'   => 'poll_'. $_poll_id,
			'option_key'   => 'tree_%'
		];
		return \lib\db\options::get($where);
	}
}
?>