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

		$qry ="
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
				CASE
					WHEN (SELECT COUNT(filter_id) FROM postfilters WHERE postfilters.post_id = posts.id) = 0 THEN
						TRUE
					ELSE
						CASE
							WHEN (SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1) IS NULL THEN
								TRUE
						ELSE
							CASE
								WHEN (SELECT COUNT(filter_id) FROM postfilters WHERE postfilters.post_id = posts.id) = 1 THEN
					  					(SELECT filter_id FROM postfilters WHERE postfilters.post_id = posts.id) =
					  					(SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1)
					  		ELSE
					  			CASE
					  				WHEN (SELECT COUNT(filter_id) FROM postfilters WHERE postfilters.post_id = posts.id) > 1 THEN
									  	(SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1) IN
									  	(SELECT filter_id FROM postfilters WHERE postfilters.post_id = posts.id)
									ELSE
										FALSE
								END
							END
						END
				END
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
										options.option_value
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