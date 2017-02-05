<?php
namespace lib\db;
use \lib\debug;
use \lib\utility;


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
		`type`				AS `type`,
		`title`				AS `title`,
		`subtype` 			AS `sub_type`,
		`true` 				AS `is_true`,
		`groupscore` 		AS `group_score`,
		`desc`				AS `description`,
		`score` 			AS `score`,
		`attachment_id` 	AS `attachment`,
		`attachmenttype` 	AS `attachment_type`
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
	public static function update($_args, $_poll_id, $_key = false)
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
			elseif(is_bool($value))
			{
				if($value)
				{
					$set[] = "`$key` = 1 ";
				}
				else
				{
					$set[] = "`$key` = 0 ";
				}
			}
		}

		if(empty($set))
		{
			return false;
		}

		$set = implode(',', $set);
		if($_key)
		{
			$query = "UPDATE pollopts SET $set WHERE post_id = $_poll_id AND pollopts.key = $_key  LIMIT 1";
		}
		else
		{
			// if key is null or false the poll id is pollopts.id
			$query = "UPDATE pollopts SET $set WHERE id = $_poll_id LIMIT 1";
		}
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

		return $result;
	}


	/**
	 * get opts of one poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_all($_poll_id, $_field = null, $_raw = false)
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
			if($_field == '*')
			{
				$field     = '*';
				$get_field = null;
			}
			else
			{
				$field     = '`'. $_field. '`';
				$get_field = $_field;
			}
		}

		$query = "SELECT $field FROM pollopts WHERE post_id = $_poll_id ORDER BY pollopts.key ASC ";
		$result = \lib\db::get($query, $get_field);
		$result = \lib\utility\filter::meta_decode($result);
		if(!$_raw)
		{
			$result = self::encode($result);
		}

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


	/**
	 * insert answers to pollopts table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function set($_poll_id, $_opts, $_options = [])
	{
		if(!$_poll_id)
		{
			return debug::error(T_("Poll id not found"), 'id', 'db');
		}

		if(!is_array($_opts))
		{
			return debug::error(T_("answers must be array"), 'answers', 'db');
		}

		$default_optsion =
		[
			'update' => false,
			'method' => 'put',
		];

		$_options = array_merge($default_optsion, $_options);

		$update = false;
		if(isset($_options['update']) && $_options['update'])
		{
			$update = true;
		}

		$must_update = [];
		$must_insert = [];
		$must_delete = [];
		$old_answers_raw = $old_answers = \lib\db\pollopts::get_all($_poll_id, '*', true);
		// var_dump($_opts, $old_answers);		exit();

		if(!$old_answers || empty($old_answers))
		{
			$must_insert = $_opts;
		}
		elseif(is_array($old_answers))
		{
			$must_delete = array_slice($old_answers, count($_opts));

			foreach ($_opts as $key => $value)
			{
				$new_key = $key + 1;
				if(isset($old_answers[$key]))
				{
					$check = self::check_update($value, $old_answers[$key]);
					if(!empty($check))
					{
						$must_update[] = $check;
					}
				}
				else
				{
					$must_insert[] = $value;
				}
			}
		}
		else
		{
			return debug::error(T_("Invalid old answers"), 'answers', 'db');
		}
		// var_dump($must_insert, $must_update, $must_delete);	exit();
		$delete_all_profile = false;
		if($_options['method'] === 'put')
		{
			$old_answers_ids = array_column($old_answers_raw, 'id');
			if(!empty($old_answers_ids))
			{
				$old_answers_ids = implode(',', $old_answers_ids);
				$query =
				"
					DELETE FROM
						termusages
					WHERE
						termusages.termusage_foreign = 'pollopts' AND
						termusages.termusage_id IN ($old_answers_ids)
				";
				$delete_all_profile = \lib\db::query($query);
			}
		}

		if(!empty($must_delete))
		{
			$ids   = array_column($must_delete, 'id');
			$ids   = implode(',', $ids);
			$query = "UPDATE pollopts SET pollopts.status = 'disable' WHERE pollopts.id IN ($ids) ";
			\lib\db::query($query);
			if(!$delete_all_profile)
			{
				$query =
				"
					DELETE FROM
						termusages
					WHERE
						termusages.termusage_foreign = 'pollopts' AND
						termusages.termusage_id IN ($ids)
				";
				\lib\db::query($query);
			}
		}
		if(!empty($must_update))
		{
			foreach ($must_update as $key => $value)
			{

				$id = array_splice($value, -1);

				if(isset($id['id']))
				{
					$id = $id['id'];
				}
				else
				{
					continue;
				}

				if(isset($value['profile']))
				{
					self::opt_profile($id, $value['profile']);
				}
				else
				{
					self::opt_profile($id, []);
				}

				$value['status'] = 'enable';

				// if(count($value) === 1 && isset($value['status']))
				// {
				// 	$value['status'] = 'disable';
				// }

				unset($value['profile']);
				self::update($value, $id);
			}
		}

		// var_dump($_poll_id);
		// exit();
		if(!empty($must_insert))
		{
			$profile = [];
			foreach ($must_insert as $key => $value)
			{
				if(count($value) === 1 && isset($value['type']))
				{
					continue;
				}

				if(isset($value['profile']))
				{
					$profile[] = $value['profile'];
				}
				else
				{
					$profile[] = [];
				}

				unset($must_insert[$key]['profile']);

				$must_insert[$key]['post_id'] = $_poll_id;
			}

			self::insert_multi($must_insert);

			$insert_id = \lib\db::insert_id();

			$ids = [];
			for ($i = ($insert_id - count($must_insert)) + 1; $i <= $insert_id; $i++)
			{
				$ids[] = $i;
			}
			if(count($ids) === count($profile))
			{
				foreach ($profile as $key => $value)
				{
					self::opt_profile($ids[$key], $value);
				}
			}
		}
		// exit();
		return true;
	}


	/**
	 * Determines if profile.
	 *
	 * @param      <type>  $_opts  The options
	 */
	private static function opt_profile($_pollopts_id, $_profile)
	{
		if($_profile === [])
		{
			$query =
			"
				DELETE FROM
					termusages
				WHERE
					termusages.termusage_foreign = 'pollopts' AND
					termusages.termusage_id      = $_pollopts_id
			";
			\lib\db::query($query);
		}
		elseif(!empty($_profile))
		{
			if(is_array($_profile))
			{
				foreach ($_profile as $key => $value)
				{
					$term_id = utility\shortURL::decode($value);
					$term = \lib\db\terms::get($term_id);
					if(!$term || !is_array($term) || !isset($term['term_type']))
					{
						return debug::error(T_("Profile code not found"), 'profile', 'arguments');
					}

					if($term['term_type'] != 'profile')
					{
						return debug::error(T_("Invalid parameter profile :code", ['code' => $value]), 'profile', 'arguments');
					}

					$query =
					"
						INSERT INTO
							termusages
						SET
							termusages.termusage_foreign = 'pollopts',
							termusages.termusage_id      = $_pollopts_id,
							termusages.term_id           = $term_id
						ON DUPLICATE KEY UPDATE
							termusages.term_id = $term_id

					";
					\lib\db::query($query);
				}
			}
		}
	}


	/**
	 * check opts change and return muse update
	 *
	 * @param      <type>  $_new_opt  The new option
	 * @param      <type>  $_old_opt  The old option
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	private static function check_update($_new_opt, $_old_opt)
	{
		$update = [];
		// var_dump(...func_get_args());
		if(is_array($_new_opt) || is_array($_old_opt))
		{
			foreach ($_old_opt as $key => $value)
			{

				if(isset($_new_opt[$key]))
				{
					switch ($key)
					{
						case 'true':
							if(boolval($value) != boolval($_new_opt[$key]))
							{
								$update[$key] = $_new_opt[$key];
							}

							break;

						default:
							if($value != $_new_opt[$key])
							{
								$update[$key] = $_new_opt[$key];
							}
							break;
					}
				}
				else
				{
					switch ($key)
					{
						case 'id':
						case 'post_id':
						case 'createdate':
						case 'datemodified':
						case 'key':
						case 'desc':
						case 'meta':
							continue;
							break;

						case 'status':
								$update[$key] = 'enable';
							break;

						case 'true':
							if($value)
							{
								$update[$key] = false;
							}
							break;

						case 'title':
						case 'type':
						case 'subtype':
						case 'groupscore':
						case 'score':
						case 'attachment_id':
						case 'attachmenttype':
						case 'profile':
						default:
							if($value)
							{
								$update[$key] = null;
							}
							break;
					}
				}
			}
		}

		if(!empty($update) && isset($_old_opt['id']))
		{
			$update['id'] = $_old_opt['id'];
			return $update;
		}

		return [];
	}
}
?>