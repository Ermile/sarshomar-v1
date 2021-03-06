<?php
namespace lib\db;

/** work with polls **/
class polls
{
	use polls\fav_like;
	use polls\get;
	use polls\insert;
	use polls\order;
	use polls\update;
	use polls\search;
	use polls\sarshomar;


	private static $_POLL = [];

	/**
	 * this library work with acoount
	 * v3.1
	 */

	public static $fields =
	"
			posts.id						AS `id`,
			posts.post_language 			AS `language`,
			posts.post_title 				AS `title`,
			posts.post_slug 				AS `slug`,
			posts.post_url 					AS `url`,
			posts.post_content 				AS `content`,
			posts.post_type 				AS `type`,
			posts.post_comment 				AS `comment`,
			posts.post_meta 				AS `meta`,
			posts.post_count 				AS `count`,
			posts.post_order 				AS `order`,
			posts.post_status 				AS `status`,
			posts.post_parent 				AS `parent`,
			posts.user_id 					AS `user_id`,
			posts.post_sarshomar			AS `sarshomar`,
			posts.post_survey 				AS `survey`,
			posts.post_privacy				AS `privacy`,
			posts.post_member				AS `member`,
			posts.post_prize				AS `prize`,
			posts.post_password				AS `password`,
			posts.post_brand				AS `brand`,
			posts.post_brandurl				AS `brandurl`,
			posts.post_prizeunit			AS `prizeunit`,
			posts.post_createdate			AS `date`,
			FALSE 							AS `have_score`,
			FALSE 							AS `have_true_answer`,

			IFNULL(posts.post_asked,0) 		AS `asked`,
			IFNULL(posts.post_hasfilter,0) 	AS `hasfilter`,
			IFNULL(posts.post_hasmedia,0) 	AS `hasmedia`,
			IFNULL(posts.post_rank,0) 		AS `count_rank`,
			IFNULL(ranks.comment,0)   		AS `count_comment`,
			IFNULL(ranks.like,0)   			AS `count_like`,
			IFNULL(ranks.fav,0)   			AS `count_fav`,
			IFNULL(ranks.skip,0)   			AS `count_skip`,
			IFNULL(ranks.vote,0)   			AS `count_vote`,
			IFNULL(ranks.filter, 0) 		AS `count_filter`,
			IFNULL(ranks.ad, 0) 			AS `count_ad`,
			IFNULL(ranks.money, 0) 			AS `count_money`,
			IFNULL(ranks.report, 0) 		AS `count_report`,
			IFNULL(ranks.view, 0) 			AS `count_view`,
			IFNULL(ranks.other, 0) 			AS `count_other`,
			IFNULL(ranks.sarshomar, 0) 		AS `count_sarshomar`,
			IFNULL(ranks.ago, 0) 			AS `count_ago`,
			IFNULL(ranks.admin, 0) 			AS `count_admin`,
			IFNULL(ranks.vip , 0) 			AS `count_vip`,
			ROUND((IFNULL(ranks.skip, 0) * 100) / IFNULL(ranks.vote, 0))			 			AS `vote_skip`
		FROM
			posts
		LEFT JOIN ranks ON ranks.post_id = posts.id
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
				answerdetails
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



	/**
	 * check the post meta of one poll
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_meta     The meta
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check_meta($_poll_id, $_meta)
	{
		$where =
		[
			'post_id'       => $_poll_id,
			'option_cat'    => 'poll_'. $_poll_id,
			'option_key'    => 'meta',
			'option_value'  => $_meta,
			'option_status' => 'enable',
			'limit'		    => 1,
		];

		$result = \lib\db\options::get($where);
		if($result && !empty($result))
		{
			return true;
		}
		return false;
	}
}
?>