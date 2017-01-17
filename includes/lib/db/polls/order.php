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
				posts.post_url LIKE '$%' AND
				-- check users not answered to this poll
				posts.id NOT IN
				(
					SELECT
						polldetails.post_id
					FROM
						polldetails
					WHERE
						polldetails.user_id = $_user_id AND
						polldetails.post_id = posts.id
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

						(
							IF(
							(
								SELECT
									COUNT(*)
								FROM
									termusages
								WHERE
									termusages.termusage_id = posts.id AND
									termusages.termusage_foreign = 'posts'
							) = 0 , posts.id , 0
						  )
						)
						,
						(
							SELECT
								IF(filter_id IS NULL, posts.id, 0)
							FROM
								users WHERE users.id = $_user_id LIMIT 1
						)
						,
						(
						IF(
							(
								SELECT
									GROUP_CONCAT(termusages.term_id SEPARATOR ' AND ')
								FROM
									termusages
								INNER JOIN terms ON termusages.term_id = terms.id
								WHERE
									termusages.termusage_id = posts.id AND
									termusages.termusage_foreign = 'posts' AND
									terms.term_type LIKE 'users%'
							)
							IN
							(
								SELECT
									term_id
								FROM
									termusages
								WHERE
									termusages.termusage_id = $_user_id AND
									termusages.termusage_foreign = 'users'
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
							polldetails.post_id = posts.post_parent AND
							CONCAT('opt_', polldetails.opt) IN
								(
									SELECT
										IFNULL(options.option_value, CONCAT('opt_', polldetails.opt))
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
			return $result[0];
		}
		return false;
	}
}
?>