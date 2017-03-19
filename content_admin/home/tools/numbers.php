<?php
namespace content_admin\home\tools;

trait numbers
{
	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function numbers()
	{
		$poll_status = "SELECT posts.post_status AS `status`, COUNT(posts.id) AS `count` FROM posts GROUP BY posts.post_status";
		$poll_status = \lib\db::get($poll_status, ['status', 'count']);

		if(is_array($poll_status))
		{
			$poll_status['total'] = array_sum($poll_status);
		}

		$user_count      = \ilib\db\users::get_count('port');
		if(is_array($user_count))
		{
			$user_count['total'] = array_sum($user_count);
		}

		$guest = 0;
		foreach ($user_count as $key => $value)
		{
			if(preg_match("/guest/", $key))
			{
				$guest += $value;
			}
		}
		$user_count['total_guest'] = $guest;

		$user_verify = "SELECT users.user_verify AS `verfify`, COUNT(users.id) AS `count` FROM users GROUP BY users.user_verify";
		$user_verify = \lib\db::get($user_verify, ['verfify', 'count']);

		if(is_array($user_verify))
		{
			$user_verify['total'] = array_sum($user_verify);
		}

		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));

		$service_id = \lib\utility\visitor::service();

		$visitor =
		"
			SELECT 'total' AS `title`, COUNT(visitors.id) AS `count` FROM visitors
			WHERE  `service_id` = $service_id AND visitor_date <= '$start' AND visitor_date >= '$end'
		UNION
			SELECT 'unique' AS `title`, COUNT(visitors.id) AS `count` FROM visitors
			WHERE  `service_id` = $service_id AND visitor_date <= '$start' AND visitor_date >= '$end' GROUP BY user_id
		UNION
			SELECT 'today' AS `title`, COUNT(visitors.id) AS `count` FROM visitors
			WHERE  `service_id` = $service_id AND DATE(visitor_date) = DATE(NOW())
		UNION
			SELECT 'last_week' AS `title`, COUNT(visitors.id) AS `count` FROM visitors
			WHERE `service_id` = $service_id AND visitor_date <= '$start' AND visitor_date >= '$end'
		";
		$visitor = \lib\db::get($visitor, ['title', 'count'], false, '[tools]');

		$result            = [];
		$result['polls']   = $poll_status;
		$result['users']   = $user_count;
		$result['verify']  = $user_verify;
		$result['visitor'] = $visitor;

		return $result;
	}
}
?>