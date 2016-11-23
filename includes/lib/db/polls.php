<?php
namespace lib\db;

/** work with polls **/
class polls
{
	use polls\faiv_like;
	use polls\get;
	use polls\insert;
	use polls\update;
	use polls\search;
	/**
	 * this library work with acoount
	 * v3.1
	 */

	public static $fields =
	"
			posts.id						as 'id',
			posts.post_language 			as 'language',
			posts.post_title 				as 'title',
			posts.post_slug 				as 'slug',
			posts.post_url 					as 'url',
			posts.post_content 				as 'content',
			posts.post_type 				as 'type',
			posts.post_comment 				as 'comment',
			posts.post_meta 				as 'meta',
			posts.post_count 				as 'count',
			posts.post_order 				as 'order',
			posts.post_status 				as 'status',
			posts.post_parent 				as 'parent',
			posts.post_publishdate 			as 'publishdate',
			posts.filter_id 				as 'filter_id',
			posts.post_sarshomar			as 'sarshomar',
			posts.date_modified  	    	as 'date_modified',
			IFNULL(posts.comment_count,0)   as 'comment_count',
			pollstats.id 		     		as 'pollstatsid',
			IFNULL(pollstats.total,0)		as 'total'
		FROM
			posts
		LEFT JOIN pollstats ON pollstats.post_id = posts.id
	";


	/**
	 * delete answers of specefic user
	 * @param  [type] $_user_id [description]
	 * @return [type]           [description]
	 */
	public static function removeUserAnswers($_user_id)
	{
		$qry = "
			DELETE FROM
				polldetails
			WHERE
				user_id = $_user_id
			-- polls::removeUserAnswers
			";
		$result = \lib\db::query($qry);
		return $result;
	}


	/**
	 * delete polls
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function delete($_post_id)
	{
		if(\lib\utility\answers::delete($_post_id))
		{
			return \lib\db\posts::delete($_poll_id);
		}
		else
		{
			return false;
		}
	}


	/**
	 * check the poll is a poll of this users of no
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  True if my poll, False otherwise.
	 */
	public static function is_my_poll($_poll_id, $_user_id)
	{
		$query =
		"
			SELECT
				user_id AS 'id'
			FROM
				posts
			WHERE
				posts.id = $_poll_id
			LIMIT 1
			-- polls::is_my_poll()
		";
		$result = \lib\db::get($query, 'id', true);
		if($result && $result == $_user_id)
		{
			return true;
		}
		return false;
	}


	/**
	 * plus the number of comments in polls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function plus_comment($_poll_id)
	{
		$query =
		"
			UPDATE
				posts
			SET
				posts.comment_count =  IF(options.comment_count IS NULL OR options.comment_count = '', 1, options.comment_count + 1)
			WHERE
				posts.id = $_poll_id
			LIMIT 1
		";
		\lib\db::query($query);
	}


	public static function check_meta($_poll_id, $_meta)
	{
		return true;
	}
}
?>