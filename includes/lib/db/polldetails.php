<?php
namespace lib\db;

/** polldetails managing **/
class polldetails
{
	/**
	 * this library work with polldetails
	 * v1.0
	 */


	/**
	 * insert new tag in polldetails table
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
				polldetails
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * insert multi value to polldetails
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value) {
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value) {
			foreach ($fields as $field_name => $vain) {
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
		$query = "
				INSERT INTO polldetails
				($fields)
				VALUES
				($values)
				";

		return \lib\db::query($query);
	}


	/**
	 * update field from polldetails table
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
				UPDATE polldetails
				SET $query
				WHERE polldetails.id = $_id;
				";

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
		$query =
		"
			SELECT
				*
			FROM
				polldetails
			WHERE
				`status` = 'enable' AND
				user_id = $_user_id
				$poll_id
		";
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
		if($_opt_index === null)
		{
			$opt = null;
		}
		else
		{
			$opt = " AND opt = '$_opt_index' ";
		}

		$query =
		"
			UPDATE
				polldetails
			SET `status` = 'disable'
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
				$opt
		";
		return \lib\db::query($query);
	}


	/**
	 * Save user answer to poll in  polldetails table.
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
		if($_opt === 'other')
		{
			$_opt = "NULL";
		}

		$default_option =
		[
			'answer_txt'  => null,
			'validation'  => 'invalid',
			'user_verify' => null,
			'port'        => 'site',
			'subport'     => null
		];
		$_option = array_merge($default_option, $_option);

		if($_option['port'] == null)
		{
			$port = "NULL";
		}
		else
		{
			$port = "'$_option[port]'";
		}

		$validation = "'invalid'";

		switch ($_option['user_verify'])
		{
			case 'mobile':
			case 'complete':
				$validation = "'valid'";
				break;

			case 'uniqueid':
				$validation = "'invalid'";
				break;

			case 'unknown':
			default:
				$validation = "NULL";
				break;
		}


		if($_option['subport'] == null)
		{
			$subport = "NULL";
		}
		else
		{
			$subport = "'$_option[subport]'";
		}

		if($_opt === 0)
		{
			$answertype = "NULL";
		}
		else
		{
			$answertype = "(SELECT pollopts.type FROM pollopts WHERE pollopts.post_id = $_poll_id AND pollopts.key = '$_opt' LIMIT 1)";
		}

		$date = date("Y-m-d H:i:s");
		$insert_polldetails =
		"
			INSERT INTO
				polldetails
			SET
				polldetails.user_id     = $_user_id,
				polldetails.post_id     = $_poll_id,
				polldetails.port        = $port,
				polldetails.subport     = $subport,
				polldetails.validstatus = $validation,
				polldetails.opt         = $_opt,
				polldetails.type        = (SELECT post_type FROM posts WHERE posts.id = $_poll_id LIMIT 1),
				polldetails.txt         = '$_option[answer_txt]',
				polldetails.profile     = (SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1),
				polldetails.insertdate  = '$date',
				polldetails.answertype  = $answertype,
				polldetails.visitor_id  = NULL

			ON DUPLICATE KEY UPDATE

				polldetails.user_id     = $_user_id,
				polldetails.post_id     = $_poll_id,
				polldetails.port        = $port,
				polldetails.subport     = $subport,
				polldetails.validstatus = $validation,
				polldetails.opt         = $_opt,
				polldetails.type        = (SELECT post_type FROM posts WHERE posts.id = $_poll_id LIMIT 1),
				polldetails.txt         = '$_option[answer_txt]',
				polldetails.profile     = (SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1),
				polldetails.insertdate  = '$date',
				polldetails.answertype  = $answertype,
				polldetails.visitor_id  = NULL,
				polldetails.status      = 'enable'
				-- answers::save_polldetails() -> $date
		";
		$result = \lib\db::query($insert_polldetails);
		return $result;
	}


	/**
	 * Gets the user count of answered or skipped
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_type     The type
	 */
	public static function user_total($_user_id, $_options = [])
	{
		$port    = "";
		$subport = "";
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => null,
			'port'    => null,
			'subport' => null
		];

		$_options = array_merge($default_options, $_options);

		switch ($_options['type']) {
			case 'answered':
				$opt = " AND polldetails.opt > 0 ";
				break;
			case 'skipped':
				$opt = " AND polldetails.opt = 0 ";
				break;
			default:
				$opt = "";
				break;
		}
		if($_options['port'])
		{
			$port = " AND polldetails.port = '$_port' ";
		}
		if($_options['subport'])
		{
			$subport = " AND polldetails.subport = '$_subport' ";
		}

		$query =
		"
			SELECT
				COUNT(polldetails.id) AS 'count'
			FROM
				polldetails
			INNER JOIN posts
				ON polldetails.post_id = posts.id AND posts.post_gender = '$_options[gender]'
			WHERE
				polldetails.status = 'enable' AND
				polldetails.user_id = $_user_id
				$opt
				$port
				$subport
		";
		$result = \lib\db::get($query, 'count', true);
		return $result;
	}


	/**
	 * get count of poll the user answer to that
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function user_total_answered($_user_id, $_options = [])
	{
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => 'answered',
			'port'    => null,
			'subport' => null
		];
		$_options = array_merge($default_options, $_options);
		return self::user_total($_user_id, $_options);
	}


	/**
	 * get count of poll the users skipped that
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function user_total_skipped($_user_id, $_options = [])
	{
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => 'skipped',
			'port'    => null,
			'subport' => null
		];
		$_options = array_merge($default_options, $_options);
		return self::user_total($_user_id, $_options);
	}


	/**
	 * get count people has answered or skipped of list of poll
	 *
	 * @param      <type>  $_poll_ids  The poll identifiers
	 * @param      <type>  $_type      The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function people($_poll_ids, $_options = [])
	{
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => null,
			'port'    => null,
			'subport' => null
		];

		$_options = array_merge($default_options, $_options);

		$port    = "";
		$subport = "";
		switch ($_options['type'])
		{
			case 'answered':
				$opt = " AND polldetails.opt > 0 ";
				break;
			case 'skipped':
				$opt = " AND polldetails.opt = 0 ";
				break;
			default:
				$opt = "";
				break;
		}

		if($_options['port'])
		{
			$port = " AND polldetails.port = '$_options[port]' ";
		}
		if($_options['subport'])
		{
			$subport = " AND polldetails.subport = '$_options[subport]' ";
		}

		$poll_ids = join($_poll_ids, ",");
		$query =
		"
			SELECT
				COUNT(polldetails.id) AS 'count'
			FROM
				polldetails
			INNER JOIN posts
				ON polldetails.post_id = posts.id AND posts.post_gender = '$_options[gender]'
			WHERE
				polldetails.status = 'enable' AND
				polldetails.post_id IN ($poll_ids)
				$opt
				$port
				$subport
		";
		$result = \lib\db::get($query, 'count', true);
		return $result;
	}


	/**
	 * get count of people was answered to list of poll
	 *
	 * @param      <type>  $_poll_ids  The poll identifiers
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function people_answered($_poll_ids, $_options = [])
	{
		$default_options =
		[
			'gender'  => 'poll',
			'type'    => 'answered',
			'port'    => null,
			'subport' => null
		];
		$_options = array_merge($default_options, $_options);
		return self::people($_poll_ids, $_options);
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
			'api_mode'  => false,
			'from'  => 0,
			'length'  => 10,
		];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$options = array_merge($default_options, $_options);

		$query ="SELECT SQL_CALC_FOUND_ROWS * FROM polldetails WHERE
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