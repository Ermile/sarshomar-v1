<?php
namespace content_admin\home\tools;

trait charts
{
	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_polls()
	{
		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));
		$language = \lib\define::get_language();

		$poll_day_chart =
		"SELECT DATE(posts.post_createdate) AS `createdate`, COUNT(posts.id) AS `count`
		FROM posts
		WHERE posts.post_createdate <= '$start' AND posts.post_createdate >= '$end'
		GROUP BY DATE(posts.post_createdate) ORDER BY DATE(posts.post_createdate) DESC";

		$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				if($language === 'fa')
				{
					$date = \lib\utility\jdate::date("Y-m-d", $key);
				}
				$result[] = ['key' => $date, 'value' => (int) $value];
			}
		}
		return $result;
	}



	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_users_week()
	{
		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-100 days'));
		$language = \lib\define::get_language();

		$poll_day_chart =
		"SELECT WEEK(users.user_createdate) AS `createdate`, COUNT(users.id) AS `count`
		FROM users
		WHERE users.user_createdate <= '$start' AND users.user_createdate >= '$end'
		GROUP BY WEEK(users.user_createdate) ORDER BY WEEK(users.user_createdate) DESC";

		$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				$result[] = ['key' => $date, 'value' => (int) $value];
			}
		}
		return $result;
	}


	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_users()
	{
		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));
		$language = \lib\define::get_language();

		$poll_day_chart =
		"SELECT DATE(users.user_createdate) AS `createdate`, COUNT(users.id) AS `count`
		FROM users
		WHERE users.user_createdate <= '$start' AND users.user_createdate >= '$end'
		GROUP BY DATE(users.user_createdate) ORDER BY DATE(users.user_createdate) DESC";

		$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				if($language === 'fa')
				{
					$date = \lib\utility\jdate::date("Y-m-d", $key);
				}
				$result[] = ['key' => $date, 'value' => (int) $value];
			}
		}
		return $result;
	}



	/**
	 * the visitor chart
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_visitor()
	{
		$chart = \lib\utility\visitor::chart();
		$result = [];
		if(is_array($chart))
		{
			$result = array_column($chart, 'total', 'date');
		}

		$language = \lib\define::get_language();
		$temp     = [];
		foreach ($result as $key => $value)
		{
			$date = $key;
			if($language === 'fa')
			{
				$date = \lib\utility\jdate::date("Y-m-d", $key);
			}
			$temp[] = ['key' => $date, 'value' => (int) $value];
		}
		return $temp;
	}




	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_answer()
	{
		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));
		$language = \lib\define::get_language();

		$poll_day_chart =
		"SELECT DATE(polldetails.insertdate) AS `createdate`, COUNT(polldetails.id) AS `count`
		FROM polldetails
		WHERE polldetails.insertdate <= '$start' AND polldetails.insertdate >= '$end'
		GROUP BY DATE(polldetails.insertdate) ORDER BY DATE(polldetails.insertdate) DESC";

		$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				if($language === 'fa')
				{
					$date = \lib\utility\jdate::date("Y-m-d", $key);
				}
				$result[] = ['key' => $date, 'value' => (int) $value];
			}
		}
		return $result;
	}


	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_transaction()
	{
		$start    = date("Y-m-d", time());
		$end      = date("Y-m-d", strtotime('-30 days'));
		$language = \lib\define::get_language();

		$poll_day_chart =
		"SELECT DATE(transactions.createdate) AS `createdate`, COUNT(transactions.id) AS `count`
		FROM transactions
		WHERE transactions.createdate <= '$start' AND transactions.createdate >= '$end'
		GROUP BY DATE(transactions.createdate) ORDER BY DATE(transactions.createdate) DESC";

		$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				if($language === 'fa')
				{
					$date = \lib\utility\jdate::date("Y-m-d", $key);
				}
				$result[] = ['key' => $date, 'value' => (int) $value];
			}
		}
		return $result;
	}
}
?>