<?php
namespace lib\db;

/** answerdetails managing **/
class answerdetails
{
	/**
	 * this library work with answerdetails
	 * v1.0
	 */


	/**
	 * insert new tag in answerdetails table
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
		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(substr($value, 0,2) === '(S')
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " `$key` = $value ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');

		$query = " INSERT INTO answerdetails SET $set ";
		return \lib\db::query($query);
	}


	/**
	 * insert multi value to answerdetails
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value)
		{
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value)
		{
			foreach ($fields as $field_name => $vain)
			{
				if(array_key_exists($field_name, $value)){
					$values[] = "'" . trim($value[$field_name]) . "'";
				}else{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values = [];
		}

		if(empty($fields))
		{
			return null;
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		// crate string query
		$query = " INSERT INTO answerdetails ($fields) VALUES ($values)	";

		return \lib\db::query($query);
	}


	/**
	 * update field from answerdetails table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		$set = \lib\db\config::make_set($_args);

		// make update query
		$query = "UPDATE answerdetails 	SET $set WHERE answerdetails.id = $_id ";

		return \lib\db::query($query);

	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return \lib\db::$_type($_query);
	}


	/**
	 * get the users answer of one poll
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function get($_user_id, $_poll_id = null)
	{
		$poll_id = " AND post_id = $_poll_id ";
		if($_poll_id === null)
		{
			$poll_id = null;
		}
		$query = " SELECT * FROM answerdetails WHERE `status` = 'enable' AND user_id = $_user_id $poll_id ";
		return \lib\db::get($query);
	}


	/**
	 * remove user answered to poll
	 * use in update result of poll
	 * we delete the poll details record and then insert the updated answer
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_user_id, $_poll_id, $_opt_index = null)
	{
		self::$USER_ID = $_user_id;
		self::$POLL_ID = $_poll_id;

		if($_opt_index === null)
		{
			$opt = null;
			\lib\db::query("UPDATE answers SET lastopt = NULL WHERE answers.user_id = $_user_id AND answers.post_id = $_poll_id");
		}
		else
		{
			$opt = " AND opt = '$_opt_index' ";
		}

		$query =
		"
			UPDATE
				answerdetails
			SET `status` = 'deleted'
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id AND
				status  = 'enable'
				$opt
		";
		return \lib\db::query($query);
	}


	/**
	 * SOME VARIABLE
	 *
	 * @var        <type>
	 */
	private static $ANSWER_IDS = [];
	private static $USER_ID    = null;
	private static $POLL_ID    = null;
	private static $NEW_ANSWER = [];


	/**
	 * Gets the answrs identifier.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 * @param      <type>   $_poll_id  The poll identifier
	 *
	 * @return     integer  The answrs identifier.
	 */
	private static function get_answrs_id($_user_id, $_poll_id, $_options = [])
	{
		$default_options =
		[
			'lastopt'    => null,
			'ask'        => false,
		];

		$_options = array_merge($default_options, $_options);

		if(!isset(self::$ANSWER_IDS[$_user_id][$_poll_id]))
		{
			$answer_id     = null;
			$get_answrs_id = "SELECT * FROM answers WHERE post_id = $_poll_id AND user_id = $_user_id LIMIT 1 ";
			$get_answrs_id = \lib\db::get($get_answrs_id, null, true);
			if(!isset($get_answrs_id['id']))
			{
				$insert_answers = "INSERT INTO answers SET user_id = $_user_id, post_id = $_poll_id";
				if(is_numeric($_options['lastopt']))
				{
					$insert_answers .= ", lastopt = $_options[lastopt] ";
				}
				if($_options['ask'])
				{
					$insert_answers .= ", ask = 1 ";
				}
				// insert to asnwers
				\lib\db::query($insert_answers);
				$answer_id = \lib\db::insert_id();
			}
			elseif(is_numeric($get_answrs_id['id']))
			{
				$answer_id = (int) $get_answrs_id['id'];
			}

			if(!$answer_id)
			{
				\lib\db\logs::set('sql:answers:id:not:fount', $_user_id, ['desc' => $_poll_id, 'meta' => ['args' => func_get_args()]]);
				debug::error(T_("System can not save your answer"), 'answers', 'system');
				return 0;
			}

			if(array_key_exists('lastopt', $get_answrs_id))
			{
				if(!$get_answrs_id['lastopt'] && is_numeric($_options['lastopt']))
				{
					\lib\db::query("UPDATE answers SET lastopt = $_options[lastopt] WHERE id = $answer_id");
				}
			}

			if(array_key_exists('ask', $get_answrs_id))
			{
				if(!$get_answrs_id['ask'] && $_options['ask'])
				{
					\lib\db::query("UPDATE answers SET ask = 1 WHERE id = $answer_id");
				}
				elseif($get_answrs_id['ask'] && !$_options['ask'])
				{
					\lib\db::query("UPDATE answers SET ask = 0 WHERE id = $answer_id");
				}
				elseif($get_answrs_id['ask'] && $_options['ask'])
				{
					// nothing
				}
				elseif(!$get_answrs_id['ask'] && !$_options['ask'])
				{
					// nothing
				}
			}

			self::$ANSWER_IDS[$_user_id][$_poll_id] = $answer_id;
		}
		return self::$ANSWER_IDS[$_user_id][$_poll_id];
	}


	/**
	 * CLEAN DATA
	 */
	public static function clean()
	{
		self::$NEW_ANSWER = [];
		self::$USER_ID    = null;
		self::$POLL_ID    = null;
	}


	/**
	 * check and save answers
	 */
	public static function check_and_save()
	{
		if(empty(self::$NEW_ANSWER))
		{
			return true;
		}

		$new_answers_key = array_keys(self::$NEW_ANSWER);
		$access_affected = \lib\utility\answers::$update_old_answer_in;
		$date_affected   = date("Y-m-d H:i:s", $access_affected);
		$answer_id       = self::get_answrs_id(self::$USER_ID, self::$POLL_ID);
		$date            = date("Y-m-d H:i:s");

		$old_saved_answer =
		"
			SELECT
				*
			FROM
				answerdetails
			WHERE
				answerdetails.answer_id = $answer_id AND
				answerdetails.status IN ('deleted', 'enable')
			ORDER BY id DESC
		";

		$old_saved_answer = \lib\db::get($old_saved_answer);

		$must_insert  = [];
		$must_update  = [];
		$must_delete  = [];
		$must_disable = [];

		// the answer can update becaus the time not left
		$access_time_answer = [];
		foreach ($old_saved_answer as $key => $value)
		{
			if(isset($value['date_affected']) && (time() - strtotime($value['date_affected'])) < $access_affected)
			{
				$access_time_answer[$key] = $value;
			}
			else
			{
				if(isset($value['id']) && isset($value['status']) && $value['status'] === 'enable')
				{
					array_push($must_disable, $value['id']);
				}
			}
		}

		$old_answer_ids   = array_column($access_time_answer, 'id');

		if(count(self::$NEW_ANSWER) === count($access_time_answer))
		{
			$must_update = array_combine($old_answer_ids, self::$NEW_ANSWER);
		}
		elseif(count(self::$NEW_ANSWER) > count($access_time_answer))
		{
			$must_insert = array_splice(self::$NEW_ANSWER, count($access_time_answer));
			$must_update = array_combine($old_answer_ids, self::$NEW_ANSWER);
		}
		elseif(count(self::$NEW_ANSWER) < count($access_time_answer))
		{
			$id_delete   = array_splice($old_answer_ids, count(self::$NEW_ANSWER));
			$must_delete = array_splice($access_time_answer, count(self::$NEW_ANSWER));
			$must_delete = array_combine($id_delete, $must_delete);
			$must_update = array_combine($old_answer_ids, self::$NEW_ANSWER);
		}

		if(!empty($must_insert))
		{
			foreach ($must_insert as $key => $value)
			{
				self::insert($value);
			}
		}

		if(!empty($must_update))
		{
			foreach ($must_update as $key => $value)
			{
				unset($value['createdate']);
				self::update($value, $key);
			}
		}

		if(!empty($must_delete))
		{
			$ids = array_keys($must_delete);
			$ids = implode(',', $ids);
			\lib\db::query("UPDATE answerdetails SET status = 'deleted' WHERE id IN ($ids)");
		}

		if(!empty($must_disable))
		{
			$ids = implode(',', $must_disable);
			\lib\db::query("UPDATE answerdetails SET status = 'disable' WHERE id IN ($ids)");
		}

		return true;
	}


	/**
	 * Save user answer to poll in  answerdetails table.
	 *
	 * @param      <type>  $_user_id        The user identifier
	 * @param      <type>  $_poll_id        The poll identifier
	 * @param      <type>  $_opt  The number of option kye
	 * @param      <type>  $_answer_txt     The answer text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function save($_user_id, $_poll_id, $_opt, $_option = [])
	{

		self::$USER_ID = $_user_id;
		self::$POLL_ID = $_poll_id;

		$default_option =
		[
			'answer_txt'  => null,
			'validation'  => 'invalid',
			'user_verify' => null,
			'port'        => 'site',
			'subport'     => null,
			'ask'         => false,
		];

		$_option = array_merge($default_option, $_option);

		if($_opt === 'other')
		{
			$_opt = null;
		}

		if($_option['port'] == null)
		{
			$port = null;
		}
		elseif(is_string($_option['port']))
		{
			$port = $_option['port'];
		}

		$validation = "'invalid'";

		switch ($_option['user_verify'])
		{
			case 'mobile':
			case 'complete':
				$validation = "valid";
				break;

			case 'uniqueid':
				$validation = "invalid";
				break;

			case 'unknown':
			default:
				$validation = null;
				break;
		}


		if($_option['subport'] == null)
		{
			$subport = null;
		}
		elseif(is_string($_option['subport']))
		{
			$subport = $_option['subport'];
		}

		if($_opt === 0)
		{
			$answertype = "NULL";
		}
		else
		{
			$answertype = "(SELECT pollopts.type FROM pollopts WHERE pollopts.post_id = $_poll_id AND pollopts.key = '$_opt' LIMIT 1)";
		}

		if(!is_numeric($_opt))
		{
			$lastopt = null;
		}
		else
		{
			$lastopt = $_opt;
		}

		$date         = date("Y-m-d H:i:s");
		$answer_id    = self::get_answrs_id($_user_id, $_poll_id, ['lastopt' => $lastopt, 'ask' => $_option['ask']]);

		$save_details =
		[
			'user_id'       => $_user_id,
			'post_id'       => $_poll_id,
			'answer_id'     => $answer_id,
			'port'          => $port,
			'subport'       => $subport,
			'validstatus'   => $validation,
			'opt'           => $_opt,
			'ask'           => $_option['ask'] ? 1 : null,
			'type'          => "(SELECT post_type FROM posts WHERE posts.id = $_poll_id LIMIT 1)",
			'txt'           => $_option['answer_txt'],
			'profile'       => "(SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1)",
			'createdate'    => $date,
			'answertype'    => $answertype,
			'visitor_id'    => null,
			'date_affected' => $date,
			'status'        => 'enable',
		];

		self::$NEW_ANSWER[$_opt] = $save_details;

		return true;
	}


	/**
	 * get count of people was skipped to list of polls
	 *
	 * @param      <type>  $_poll_ids  The poll identifiers
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function people_skipped($_poll_ids, $_options = [])
	{
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => 'skipped',
			'port'    => null,
			'subport' => null
		];
		$_options = array_merge($default_options, $_options);
		return self::people($_poll_ids, $_options);
	}

	public static function get_answrs_list($_poll_id, $_options = [])
	{

		$default_options =
		[
			'api_mode' => false,
			'from'     => 0,
			'length'   => 10,
		];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$options = array_merge($default_options, $_options);

		$query ="SELECT SQL_CALC_FOUND_ROWS * FROM answerdetails WHERE
			`status` = 'enable' AND
			`post_id` = $_poll_id
			LIMIT $options[from], $options[length]
			";

		$get = \lib\db::get($query);
		if($options['api_mode'] == true)
		{
			$found_rows = \lib\db::get("SELECT FOUND_ROWS() AS `total`", 'total', true);
			\lib\storage::set_total_record($found_rows);
		}
		return $get;
	}
}
?>