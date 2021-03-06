<?php
namespace content_admin\home\tools;

trait numbers
{
	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function update_number()
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
		if(isset($user_verify['']))
		{
			$user_verify['unknown'] = $user_verify[''];
			unset($user_verify['']);
		}

		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));

		$service_id = \lib\utility\visitor::service();

		$result            = [];
		$result['polls']   = $poll_status;
		$result['users']   = $user_count;
		$result['verify']  = $user_verify;

		$result['visitor'] = [];

		$result['visitor']['total'] = \lib\db::get("SELECT COUNT(visitors.id) AS `count` FROM visitors	WHERE  `service_id` = $service_id AND visitor_date >= '$end'", 'count', true, '[tools]');
		$result['visitor']['unique'] = \lib\db::get("SELECT  COUNT(DISTINCT visitors.user_id) AS `count` FROM visitors	WHERE  `service_id` = $service_id AND visitor_date >= '$end' ", 'count', true, '[tools]');
		$result['visitor']['today'] = \lib\db::get("SELECT COUNT(visitors.id) AS `count` FROM visitors	WHERE  `service_id` = $service_id AND DATE(visitor_date) = DATE(NOW())", 'count', true, '[tools]');
		$result['visitor']['last_week'] = \lib\db::get("SELECT COUNT(visitors.id) AS `count` FROM visitors	WHERE `service_id` = $service_id AND visitor_date >= '$end'", 'count', true, '[tools]');
		$insert = [];
		foreach ($result as $key => $value)
		{
			$where =
			[
				'post_id'      => null,
				'user_id'      => null,
				'option_cat'   => 'admin_dashboard',
				'option_key'   => 'numbers',
				'option_value' => $key,
			];
			$check = \lib\db\options::get($where);
			if(empty($check))
			{
				$where['option_meta']  = json_encode($value, JSON_UNESCAPED_UNICODE);
				$result = \lib\db\options::insert($where);
			}
			else
			{
				$args = ['option_meta' => json_encode($value, JSON_UNESCAPED_UNICODE)];
				\lib\db\options::update_on_error($args, $where);
			}
		}
		return $result;
	}

	/**
	 * get static numbers of admin dashboard
	 *
	 * @param      boolean  $_update  The update
	 */
	public function numbers($_update = false)
	{
		$query =
			"SELECT options.option_meta AS `meta`, options.option_value AS `key`  FROM options
			WHERE options.option_cat = 'admin_dashboard' AND options.option_key = 'numbers' AND options.option_status = 'enable' ";
		$result = \lib\db::get($query, ['key', 'meta']);
		if(empty($result) || $_update)
		{
			$result = $this->update_number();
		}
		elseif(is_array($result))
		{
			$temp = [];
			foreach ($result as $key => $value)
			{
				$temp[$key] = json_decode($value, true);
			}
			$result = $temp;
		}
		else
		{
			$result = [];
		}
	 	return $result;
	}
}
?>