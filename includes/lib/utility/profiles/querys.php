<?php
namespace lib\utility\profiles;

trait querys
{
	/**
	 * query needet to update dashboard data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	private static function dashboard_query($_user_id)
	{
		$querys = [];
		$querys['poll_answered'] =
		"
			SELECT COUNT(answerdetails.id)
			FROM answerdetails
			WHERE
				answerdetails.user_id = $_user_id AND
				answerdetails.type    = 'poll' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt <> 0
		";
		$querys['poll_skipped'] =
		"
			SELECT COUNT(answerdetails.id)
			FROM answerdetails
			WHERE
				answerdetails.user_id = $_user_id AND
				answerdetails.type    = 'poll' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt     = 0
		";
		$querys['survey_answered'] =
		"
			SELECT COUNT(answerdetails.id)
			FROM answerdetails
			WHERE
				answerdetails.user_id = $_user_id AND
				answerdetails.type    = 'survey' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt <> 0
		";
		$querys['survey_skipped'] =
		"
			SELECT COUNT(answerdetails.id)
			FROM answerdetails
			WHERE
				answerdetails.user_id = $_user_id AND
				answerdetails.type    = 'survey' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt     = 0
		";
		$querys['my_poll'] =
		"
			SELECT COUNT(posts.id)
			FROM posts
			WHERE
				posts.user_id   = $_user_id AND
				posts.post_type = 'poll' AND
				posts.post_status IN ('draft', 'publish', 'awaiting','trash','pause', 'stop')
		";
		$querys['my_survey'] =
		"
			SELECT COUNT(posts.id)
			FROM posts
			WHERE
				posts.user_id   = $_user_id AND
				posts.post_type = 'survey' AND
				posts.post_status IN ('draft', 'publish', 'awaiting','trash','pause', 'stop')
		";
		$querys['my_poll_answered']   =
		"
			SELECT
				COUNT(answerdetails.id)
			FROM
				answerdetails
			INNER JOIN posts
				ON answerdetails.post_id = posts.id AND posts.post_type = 'poll'
			WHERE
				answerdetails.type    = 'poll' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt <> 0 AND
				answerdetails.post_id IN
					(
						SELECT posts.id
						FROM posts
						WHERE
							posts.user_id   = $_user_id AND
							posts.post_type = 'poll'
					)
		";
		$querys['my_poll_skipped']    =
		"
			SELECT
				COUNT(answerdetails.id)
			FROM
				answerdetails
			INNER JOIN posts
				ON answerdetails.post_id = posts.id AND posts.post_type = 'poll'
			WHERE
				answerdetails.type   = 'poll' AND
				answerdetails.status = 'enable' AND
				answerdetails.opt    = 0 AND
				answerdetails.post_id IN
					(
						SELECT posts.id
						FROM posts
						WHERE
							posts.user_id   = $_user_id AND
							posts.post_type = 'poll'
					)
		";
		$querys['my_survey_answered'] =
		"
			SELECT
				COUNT(answerdetails.id)
			FROM
				answerdetails
			INNER JOIN posts
				ON answerdetails.post_id = posts.id AND posts.post_type = 'survey'
			WHERE
				answerdetails.type    = 'survey' AND
				answerdetails.status  = 'enable' AND
				answerdetails.opt <> 0 AND
				answerdetails.post_id IN
					(
						SELECT posts.id
						FROM posts
						WHERE
							posts.user_id   = $_user_id AND
							posts.post_type = 'survey'
					)
		";
		$querys['my_survey_skipped']  =
		"
			SELECT
				COUNT(answerdetails.id)
			FROM
				answerdetails
			INNER JOIN posts
				ON answerdetails.post_id = posts.id AND posts.post_type = 'survey'
			WHERE
				answerdetails.type   = 'survey' AND
				answerdetails.status = 'enable' AND
				answerdetails.opt    = 0 AND
				answerdetails.post_id IN
					(
						SELECT posts.id
						FROM posts
						WHERE
							posts.user_id   = $_user_id AND
							posts.post_type = 'survey'
					)
		";
		$querys['user_referred'] =
		"
			SELECT
				COUNT(users.id)
			FROM
				users
			WHERE
				users.user_parent = $_user_id
		";
		$querys['user_verified'] =
		"
			SELECT
				COUNT(users.id)
			FROM
				users
			WHERE
				users.user_parent = $_user_id AND
				users.user_status = 'active'
		";
		$querys['comment_count'] =
		"
			SELECT COUNT(comments.id) FROM comments WHERE comments.user_id = $_user_id
		";
		$querys['draft_count'] =
		"
			SELECT COUNT(posts.id)
			FROM posts
			WHERE
				posts.user_id     = $_user_id AND
				posts.post_status = 'draft'
		";
		$querys['publish_count'] =
		"
			SELECT COUNT(posts.id)
			FROM posts
			WHERE
				posts.user_id     = $_user_id AND
				posts.post_status = 'publish'
		";
		$querys['awaiting_count'] =
		"
			SELECT COUNT(posts.id)
			FROM posts
			WHERE
				posts.user_id     = $_user_id AND
				posts.post_status = 'awaiting'
		";

		return $querys;
	}
}
?>