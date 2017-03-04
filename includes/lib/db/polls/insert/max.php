<?php
namespace lib\db\polls\insert;
use \lib\debug;

trait max
{
	/**
	 * check max draft for every user
	 */
	private static function max_draft()
	{
		$user_id = self::$user_id;
		$query =
		"
			SELECT
				COUNT(posts.id) AS `count`
			FROM
				posts
			WHERE
				posts.post_status = 'draft' AND
				posts.user_id = $user_id AND
				posts.post_survey IS NULL
		";
		$count = (int) \lib\db::get($query, 'count', true);

		$max_draft  = 5;

		if(self::poll_check_permission('u','draft_poll_10', 'view'))
		{
			$max_draft = 10;
		}

		if(self::poll_check_permission('u','draft_poll_50', 'view'))
		{
			$max_draft = 50;
		}
		if(self::poll_check_permission('u','draft_poll_500', 'view'))
		{
			$max_draft = 500;
		}

		if(self::poll_check_permission('u','draft_poll_max', 'view'))
		{
			$max_draft = 1000000;
		}

		if($count > $max_draft)
		{
			return debug::error(T_("You can save :max draft poll",['max' => $max_draft]), false, false);
		}
	}


	/**
	 * max chilren of one survey
	 *
	 * @param      <type>  $_parent_id  The parent identifier
	 */
	private static function max_survey_child($_parent_id)
	{
		$user_id = self::$user_id;
		$query =
		"
			SELECT
				COUNT(posts.id) AS `count`
			FROM
				posts
			WHERE
				posts.post_status = 'draft' AND
				posts.user_id     = $user_id AND
				posts.post_survey = $_parent_id
		";
		$count = (int) \lib\db::get($query, 'count', true);

		$max_survey_child        = 500;

		if($count > $max_survey_child)
		{
			return debug::error(T_("You can save :max poll in one survey",['max' => $max_survey_child]), false, false);
		}
	}
}
?>