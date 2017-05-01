<?php
namespace lib\db\polls;

trait order
{
	/**
	 * plus asked me on posts
	 *
	 * @param      <type>  $_post_id  The post identifier
	 */
	public static function plus_asked($_post_id)
	{
		if(!$_post_id || !is_numeric($_post_id))
		{
			return false;
		}

		\lib\db::query(
		"UPDATE posts
		SET posts.post_asked =
		IF(posts.post_asked IS NULL
		OR posts.post_asked = '', 1 , posts.post_asked + 1)
		WHERE posts.id = $_post_id
		LIMIT 1");
	}


	/**
	 * plus asked me on posts
	 *
	 * @param      <type>  $_post_id  The post identifier
	 */
	public static function minus_asked($_post_id)
	{
		if(!$_post_id || !is_numeric($_post_id))
		{
			return false;
		}

		\lib\db::query(
		"UPDATE posts
		SET posts.post_asked =
		IF(posts.post_asked IS NULL
		OR posts.post_asked = ''
		OR posts.post_asked < 1, 0 , posts.post_asked - 1)
		WHERE posts.id = $_post_id
		LIMIT 1");
	}


	/**
	 * check user answer less than 20 poll in 24 hours
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function access_ask_me($_user_id)
	{
		// check count answer user in last 24 hours
		$first_time = time() - (60 * 60 * 24);
		$now        = date("Y-m-d H:i:s");
		$first_time = date("Y-m-d H:i:s", $first_time);

		$query =
		"
			SELECT
				COUNT(DISTINCT answers.post_id) AS `count`
			FROM
				answers
			WHERE
				answers.user_id  = $_user_id AND
				answers.createdate >= '$first_time' AND
				answers.createdate < '$now'
		";

		$count = \lib\db::get($query, 'count', true);

		if(intval($count) > 200)
		{
			$_SESSION['ask_me_limit'] = true;
			\lib\db\logs::set('user:ask:me:limit:20count:20hours', $_user_id);
			\lib\debug::warn(T_("You can answer 20 poll in 24 hours"));
			return false;
		}
		return true;
	}

	/**
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function get_last($_user_id)
	{
		if(!self::access_ask_me($_user_id))
		{
			return false;
		}

		if(!$_user_id)
		{
			return false;
		}
		// the user has filter
		$user_has_filter = 'FALSE';

		$user_filter = \lib\db::get(
		"
			SELECT DISTINCT term_id AS `term_id`
			FROM
				termusages
			WHERE
				termusages.termusage_id = $_user_id AND
				(
					termusages.termusage_foreign = 'profile' OR
					termusages.termusage_foreign = 'user_profile'
				) AND
				termusages.termusage_status  = 'enable'
		", 'term_id');

		if(is_array($user_filter) && !empty($user_filter))
		{
			$count_user_filter = count($user_filter);
			$user_filter       = implode(',', $user_filter);
			$user_has_filter = "TRUE";
		}
		else
		{
			$count_user_filter = 0;
			$user_filter       = "NULL";
		}

		$language = \lib\define::get_language();

		$public_fields = self::$fields;
		$qry =
		"
			SELECT
				posts.id AS `ask_id`
			FROM
				posts
			WHERE
			-- check post condition
			posts.post_status = 'publish'
			AND (posts.post_type = 'poll' OR posts.post_type = 'survey')
			AND posts.post_language = '$language'
			AND posts.post_privacy = 'public'
			AND (posts.post_member > posts.post_asked)
			-- check users not answered to this poll
			AND
			(
				SELECT answers.id
				FROM answers
				WHERE
					answers.user_id = $_user_id AND
					answers.post_id = posts.id AND
					answers.lastopt IS NOT NULL
			) IS NULL
			-- Check poll tree
			AND
			(
				CASE
					-- If this poll not in tree  return true
					WHEN posts.post_parent IS NULL THEN TRUE
					ELSE
						-- Check this users answered to parent of this poll and her answer is important in tree
						posts.post_parent IN
						(
							SELECT
								answerdetails.post_id
							FROM
								answerdetails
							WHERE
								answerdetails.user_id = $_user_id AND
								answerdetails.post_id = posts.post_parent AND
								answerdetails.status  = 'enable' AND
								answerdetails.opt IN
								(
									SELECT
										IF(polltrees.opt IS NULL, answerdetails.opt, polltrees.opt)
									FROM
										polltrees
									WHERE
										polltrees.post_id = posts.id AND
										polltrees.parent  = posts.post_parent
								)
						)
				END
			)
			-- Check post filter
			AND
			(
				CASE
					-- If this poll has not filter  return true
					WHEN posts.post_hasfilter = 0 THEN TRUE
					WHEN posts.post_hasfilter = 1 AND $user_has_filter = FALSE THEN FALSE
					ELSE
					(
						SELECT termusages.term_id AS `filter_of_post`
						FROM termusages
						WHERE
						termusages.term_id IN ($user_filter) AND
						termusages.termusage_id      = posts.id AND
						termusages.termusage_foreign = 'filter' AND
						termusages.termusage_status  = 'enable'
						GROUP BY filter_of_post
						HAVING COUNT(DISTINCT filter_of_post) = $count_user_filter
					)
				END
			)
			ORDER BY posts.post_rank DESC, posts.id ASC
			LIMIT 1
			-- ASK QUERY --
			-- polls::get_last()
		";
		$result = \lib\db::get($qry, 'ask_id', true);

		if($result)
		{
			$result = self::get_poll($result);
			self::set_user_ask_me_on($_user_id, $result);
			return $result;
		}
		self::set_user_ask_me_on($_user_id, false);
		return false;
	}


	/**
	 * Saves an user ask me on.
	 *
	 * @param      <type>  $_poll_data  The poll data
	 */
	public static function set_user_ask_me_on($_user_id, $_poll_data)
	{

		$log_meta =
		[
			'data'=> null,
			'meta' =>
			[
				'poll' => $_poll_data
			],
		];

		if($_poll_data === false)
		{
			\lib\db\logs::set('user:request:ask:empty', $_user_id, $log_meta);
		}

		$poll_id = 0;
		if(isset($_poll_data['id']))
		{
			$poll_id = $_poll_data['id'];
		}

		\lib\utility\users::set_ask($_user_id, $poll_id);
		return;

		// $cat = 'user_detail_'. $_user_id;
		// $where =
		// [
		// 	'post_id'      => null,
		// 	'user_id'      => $_user_id,
		// 	'option_cat'   => $cat,
		// 	'option_key'   => 'user_ask_me',
		// 	'limit'        => 1,
		// ];


		// $exist_option_record = \lib\db\options::get($where);

		// if(isset($exist_option_record['value']) && (int) $exist_option_record['value'] === (int) $poll_id)
		// {
		// 	return ;
		// }

		// unset($where['limit']);
		// $args = $where;
		// unset($args['post_id']);
		// if(!$exist_option_record)
		// {
		// 	$args['option_value'] = $poll_id;
		// 	\lib\db\options::insert($args);
		// }
		// else
		// {
		// 	\lib\db\options::update_on_error(['option_value' => $poll_id], $where);
		// }
	}

	public static $ASK_ME_ON = [];

	/**
	 * Gets the user ask me on.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  The user ask me on.
	 */
	public static function get_user_ask_me_on($_user_id)
	{
		if(!isset(self::$ASK_ME_ON[$_user_id]))
		{

			self::$ASK_ME_ON[$_user_id] =  (int) \lib\utility\users::get_ask($_user_id);
			// $cat = 'user_detail_'. $_user_id;
			// $args =
			// [
			// 	'post_id'      => null,
			// 	'user_id'      => $_user_id,
			// 	'option_cat'   => $cat,
			// 	'option_key'   => 'user_ask_me',
			// 	'limit'        => 1,
			// ];

			// $result = \lib\db\options::get($args);

			// if(empty($result) || !isset($result['value']))
			// {
			// 	self::$ASK_ME_ON[$_user_id] =  false;
			// }
			// self::$ASK_ME_ON[$_user_id] =  (int) $result['value'];
		}
		return self::$ASK_ME_ON[$_user_id];
	}
}
?>