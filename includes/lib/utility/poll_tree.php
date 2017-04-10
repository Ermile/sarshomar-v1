<?php
namespace lib\utility;

class poll_tree
{
	/**
	 * Sets the poll tree.
	 *
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

		// check parent poll
		$parent_poll = \lib\db\polls::get_poll($parent);
		if(!$parent_poll)
		{
			return \lib\debug::error(T_("Parent poll not found"), 'parent_id', 'arguments');
		}
		if(isset($parent_poll['type']) && $parent_poll['type'] != 'poll' && $parent_poll['type'] != 'survey')
		{
			return \lib\debug::error(T_("Parent id is not a poll or survey"), 'parent_id', 'arguments');
		}

		if(isset($parent_poll['status']) && $parent_poll['status'] != 'publish')
		{
			if(isset($_args['user_id']) && isset($parent_poll['user_id']) && $_args['user_id'] != $parent_poll['user_id'])
			{
				return \lib\debug::error(T_("Can not set tree on :status poll", ['status' => $parent_poll['status']]), 'parent_id', 'arguments');
			}
		}

		$opt = true;

		if(isset($_args['opt']))
		{
			$opt = $_args['opt'];
		}

		if(!is_array($opt))
		{
			$opt = [$opt];
		}

		if(isset($_args['child']))
		{
			$child = $_args['child'];
		}
		else
		{
			return false;
		}

		$option_result = true;

		$parent_poll_opt = \lib\db\pollopts::get($parent);

		if(is_array($parent_poll_opt))
		{
			$parent_poll_opt = array_column($parent_poll_opt, 'key');
		}
		else
		{
			$parent_poll_opt = [];
		}

		$insert = [];
		foreach ($opt as $key => $value)
		{
			if($value !== true && !in_array($value, $parent_poll_opt))
			{
				return \lib\debug::error(T_("The parent poll have not answer :key", ['key' => $value]),'tree', 'arguments');
			}

			if($value === true)
			{
				$insert[] = " ($parent, $child, NULL) ";
			}
			else
			{
				$insert[] = " ($parent, $child, $value) ";
			}
		}

		if(!empty($insert))
		{
			$insert = implode(',', $insert);
			\lib\db::query("INSERT INTO polltrees (parent, post_id, opt) VALUES $insert ");
		}

		// foreach ($opt as $key => $value)
		// {

		// 	$option_insert =
		// 	[
		// 		'post_id'      => $child,
		// 		'option_cat'   => 'poll_'. $child,
		// 		'option_key'   => 'tree_'. $parent,
		// 		'option_value' => ($value === true) ? 'true' : $value,
		// 		'limit'        => 1,
		// 	];

		// 	$check = \lib\db\options::get($option_insert);
		// 	unset($option_insert['limit']);

		// 	if($check)
		// 	{

		// 		$where                          = $option_insert;
		// 		$option_insert['option_status'] = 'enable';
		// 		$option_result = \lib\db\options::update_on_error($option_insert, $where);
		// 	}
		// 	else
		// 	{
		// 		$option_result = \lib\db\options::insert($option_insert);
		// 	}
		// }

		$update_poll = ['post_parent' => $parent];

		$result        = \lib\db\posts::update($update_poll, $child);

		if($option_result && $result)
		{
			return true;
		}
		else
		{
			return \lib\debug::error(T_("Can not set tree poll"), false, 'sql');
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
		\lib\db\posts::update(['post_parent' => null], $_poll_id);
		$query = "DELETE FROM polltrees WHERE polltrees.post_id = $_poll_id ";
		return \lib\db::query($query);
	}


		/**
	 * remove poll tree
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove2($_poll_id)
	{

		$update_poll =
		[
			'post_parent' => null,
		];

		$result = \lib\db\posts::update($update_poll, $_poll_id);

		$where =
		[
			'post_id'    => $_poll_id,
			'option_cat' => "poll_$_poll_id",
			'option_key' => 'tree%'
		];

		$result = \lib\db\options::update_on_error(['option_status' => 'disable'], $where);
		return $result;
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
	public static function get2($_poll_id)
	{
		$where =
		[
			'post_id'      => $_poll_id,
			'option_cat'   => 'poll_'. $_poll_id,
			'option_key'   => 'tree%'
		];
		return \lib\db\options::get($where);
	}

	/**
	 * get the record of poll tree in option table
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id, $_raw = false)
	{
		if($_raw)
		{
			return \lib\db::get("SELECT * FROM polltrees WHERE polltrees.post_id = $_poll_id");
		}
		else
		{
			$return = [];
			$tree = \lib\db::get(
			"SELECT
				posts.id 		  AS `parent`,
				posts.post_url    AS `url`,
				posts.post_title  AS `title`,
				polltrees.opt 	  AS `answers`
			FROM polltrees
			JOIN posts ON posts.id = polltrees.parent
			WHERE polltrees.post_id = $_poll_id");
			if(is_array($tree))
			{
				if(isset($tree[0]['parent']))
				{
					$return['parent'] = \lib\utility\shortURL::encode($tree[0]['parent']);
				}

				if(isset($tree[0]['title']))
				{
					$return['title'] = $tree[0]['title'];
				}

				$host = Protocol."://" . \lib\router::get_root_domain(). \lib\define::get_current_language_string();

				if(isset($tree[0]['url']))
				{
					$return['url'] = $host. '/'. $tree[0]['url'];
				}

				$return['answers'] = array_column($tree, 'answers');
			}
			return $return;
		}
	}
}
?>