<?php
namespace lib\db\polls;

trait order
{

	/**
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function get_last($_user_id)
	{
		$language = \lib\define::get_language();

		$public_fields = self::$fields;

		$qry =
		"
			SELECT
				$public_fields
			-- To get options of this poll
			LEFT JOIN options ON options.post_id = posts.id
			WHERE
				posts.post_status = 'publish' AND
				posts.post_type IN ('poll', 'survey') AND
				-- check users not answered to this poll
				posts.id NOT IN
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
			-- Check the poll language
			AND (posts.post_language IS NULL OR posts.post_language = '$language')
			-- Check public poll
			AND posts.post_privacy = 'public'
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
										(
											CASE options.option_value
												WHEN 'true' 	THEN polldetails.opt
												WHEN 'skipped' 	THEN 0
												ELSE options.option_value
											END
										)
									FROM
										options
									WHERE
										options.post_id    = posts.id AND
										options.option_cat = CONCAT('poll_', posts.id) AND
										options.option_key = CONCAT('tree_', posts.post_parent) AND
										options.user_id IS NULL
								)
					)
				END
			ORDER BY posts.post_rank DESC, posts.id ASC
			LIMIT 1
			-- polls::get_last()
			-- get next poll to answer user
		";
		$result = \lib\db::get($qry, null);
		$result = \lib\utility\filter::meta_decode($result);
		if(isset($result[0]))
		{
			self::set_user_ask_me_on($_user_id, $result[0]);
			return $result[0];
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

		$args = $where;

		$exist_option_record = \lib\db\options::get($where);

		if(isset($exist_option_record['value']) && (int) $exist_option_record['value'] === (int) $poll_id)
		{
			return ;
		}

		unset($args['limit']);
		unset($args['post_id']);
		unset($where['limit']);

		$args['option_value'] = $poll_id;

		if(!$exist_option_record)
		{
			\lib\db\options::insert($args);
		}
		else
		{
			\lib\db\options::update_on_error($args, $where);
		}
	}


	/**
	 * Gets the user ask me on.
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  The user ask me on.
	 */
	public static function get_user_ask_me_on($_user_id)
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
			return false;
		}
		return (int) $result['value'];
	}
}
?>