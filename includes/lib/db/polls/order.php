<?php
namespace lib\db\polls;

trait order
{
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
				COUNT(DISTINCT polldetails.post_id) AS `count`
			FROM
				polldetails
			WHERE
				polldetails.insertdate >= '$first_time' AND
				polldetails.insertdate < '$now' AND
				polldetails.user_id  = $_user_id
		";

		$count = \lib\db::get($query, 'count', true);

		if(intval($count) > 20)
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
			AND posts.post_type IN ('poll', 'survey')
			AND (posts.post_language IS NULL OR posts.post_language = '$language')
			AND posts.post_privacy = 'public'
			-- check users not answered to this poll
			AND	posts.id NOT IN
			(
				SELECT
					polldetails.post_id
				FROM
					polldetails
				WHERE
					polldetails.user_id = $_user_id AND
					polldetails.post_id = posts.id AND
					polldetails.status  = 'enable'
			)
			-- Check poll tree
			AND
				CASE
					-- If this poll not in tree  return true
					WHEN posts.post_parent IS NULL THEN TRUE
				ELSE
					-- Check this users answered to parent of this poll and her answer is important in tree
					posts.post_parent IN
					(
						SELECT
							polldetails.post_id
						FROM
							polldetails
						WHERE
							polldetails.user_id = $_user_id AND
							polldetails.status  = 'enable' AND
							polldetails.post_id = posts.post_parent AND
							polldetails.opt IN
							(
								SELECT
									IF(polltrees.opt IS NULL, polldetails.opt, polltrees.opt)
								FROM
									polltrees
								WHERE
									polltrees.post_id = posts.id
							)
					)
				END
			-- Check post filter
			AND
				posts.id IN
				(
					CONCAT_WS(',',
						-- if this post have not eny filter return posts.id to load it
						(
							IF(
							(
								SELECT
									COUNT(*)
								FROM
									termusages
								WHERE
									termusages.termusage_id = posts.id AND
									termusages.termusage_foreign = 'filter'
							) = 0 , posts.id , 0
						  )
						)
						,
						-- if the user have not eny filter return posts.id to load it
						(
							SELECT
								IF(users.filter_id IS NULL, posts.id, 0)
							FROM
								users WHERE users.id = $_user_id LIMIT 1
						)
						,
						-- this poll have filter and user have filter
						-- check the poll filter in user filter
						-- then load this poll by return posts.id
						(
						IF(
							(
								SELECT
									GROUP_CONCAT(termusages.term_id SEPARATOR ' AND ')
								FROM
									termusages
								INNER JOIN terms ON termusages.term_id = terms.id
								WHERE
									termusages.termusage_id      = posts.id AND
									termusages.termusage_foreign = 'filter' AND
									termusages.termusage_status  = 'enable' AND
									terms.term_type              = 'sarshomar'
							)
							IN
							(
								SELECT
									term_id
								FROM
									termusages
								WHERE
									termusages.termusage_id      = $_user_id AND
									termusages.termusage_foreign = 'profile' AND
									termusages.termusage_status  = 'enable'
							),
							posts.id, 0
							)
						)
					)
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

		$cat = 'user_detail_'. $_user_id;
		$where =
		[
			'post_id'      => null,
			'user_id'      => $_user_id,
			'option_cat'   => $cat,
			'option_key'   => 'user_ask_me',
			'limit'        => 1,
		];


		$exist_option_record = \lib\db\options::get($where);

		if(isset($exist_option_record['value']) && (int) $exist_option_record['value'] === (int) $poll_id)
		{
			return ;
		}

		unset($where['limit']);
		$args = $where;
		unset($args['post_id']);
		if(!$exist_option_record)
		{
			$args['option_value'] = $poll_id;
			\lib\db\options::insert($args);
		}
		else
		{
			\lib\db\options::update_on_error(['option_value' => $poll_id], $where);
		}
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
			$cat = 'user_detail_'. $_user_id;
			$args =
			[
				'post_id'      => null,
				'user_id'      => $_user_id,
				'option_cat'   => $cat,
				'option_key'   => 'user_ask_me',
				'limit'        => 1,
			];

			$result = \lib\db\options::get($args);

			if(empty($result) || !isset($result['value']))
			{
				self::$ASK_ME_ON[$_user_id] =  false;
			}
			self::$ASK_ME_ON[$_user_id] =  (int) $result['value'];
		}
		return self::$ASK_ME_ON[$_user_id];
	}
}
?>