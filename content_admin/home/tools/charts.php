<?php
namespace content_admin\home\tools;

trait charts
{
	public $language = null;
	/**
	 * update chart data
	 */
	public function chart_update($_key)
	{
		$language = \lib\define::get_language();
		switch ($_key)
		{
			case 'polls':
				$end      = date("Y-m-d", strtotime('-30 days'));
				$poll_day_chart =
				"SELECT DATE(posts.post_createdate) AS `createdate`, COUNT(posts.id) AS `count`
				FROM posts	WHERE  posts.post_createdate >= '$end'
				GROUP BY DATE(posts.post_createdate) ORDER BY DATE(posts.post_createdate) DESC";
				$poll_day_chart = \lib\db::get($poll_day_chart, ['createdate', 'count']);
				$this->chart_save_options('polls', $poll_day_chart);
				return $poll_day_chart;
				break;

			case 'users_week':
				$end      = date("Y-m-d", strtotime('-100 days'));
				$users_week =
				"SELECT WEEK(users.user_createdate) AS `createdate`, COUNT(users.id) AS `count`
				FROM users
				WHERE  users.user_createdate >= '$end'
				GROUP BY WEEK(users.user_createdate) ORDER BY WEEK(users.user_createdate) DESC";

				$users_week = \lib\db::get($users_week, ['createdate', 'count']);
				$this->chart_save_options('users_week', $users_week);
				return $users_week;
				break;

			case 'users':
				$end      = date("Y-m-d", strtotime('-30 days'));
				$users =
				"SELECT DATE(users.user_createdate) AS `createdate`, COUNT(users.id) AS `count`
				FROM users
				WHERE  users.user_createdate >= '$end'
				GROUP BY DATE(users.user_createdate) ORDER BY DATE(users.user_createdate) DESC";
				$users = \lib\db::get($users, ['createdate', 'count']);
				$this->chart_save_options('users', $users);
				return $users;
				break;

			case 'visitor':
				$chart = \lib\utility\visitor::chart();
				$result = [];
				if(is_array($chart))
				{
					$result = array_column($chart, 'total', 'date');
				}
				$this->chart_save_options('visitor', $result);
				return $result;
				break;

			case 'answer':
				$end      = date("Y-m-d", strtotime('-30 days'));
				$answer =
				"SELECT DATE(polldetails.insertdate) AS `createdate`, COUNT(polldetails.id) AS `count`
				FROM polldetails
				WHERE  polldetails.insertdate >= '$end'
				GROUP BY DATE(polldetails.insertdate) ORDER BY DATE(polldetails.insertdate) DESC";
				$answer = \lib\db::get($answer, ['createdate', 'count']);
				$this->chart_save_options('answer', $answer);
				return $answer;

			case 'transactions':
				$end      = date("Y-m-d", strtotime('-30 days'));
				$transactions =
				"SELECT DATE(transactions.createdate) AS `createdate`, COUNT(transactions.id) AS `count`
				FROM transactions
				WHERE  transactions.createdate >= '$end'
				GROUP BY DATE(transactions.createdate) ORDER BY DATE(transactions.createdate) DESC";
				$transactions = \lib\db::get($transactions, ['createdate', 'count']);
				$this->chart_save_options('transactions', $transactions);
				return $transactions;

			default:
				return [];
				break;
		}
	}


	/**
	 * save options
	 */
	private function chart_save_options($_key, $_data)
	{
		$where =
		[
			'post_id'      => null,
			'user_id'      => null,
			'option_cat'   => 'admin_dashboard',
			'option_key'   => 'charts',
			'option_value' => $_key,
		];
		$check = \lib\db\options::get($where);
		if(empty($check))
		{
			$where['option_meta']  = json_encode($_data, JSON_UNESCAPED_UNICODE);
			$result = \lib\db\options::insert($where);
		}
		else
		{
			$args = ['option_meta' => json_encode($_data, JSON_UNESCAPED_UNICODE)];
			\lib\db\options::update_on_error($args, $where);
		}
	}


	/**
	 * get chart data
	 *
	 * @param      <type>  $_key   The key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function chart_get_options($_key)
	{
		$this->language = \lib\define::get_language();
		$where =
		[
			'post_id'      => null,
			'user_id'      => null,
			'option_cat'   => 'admin_dashboard',
			'option_key'   => 'charts',
			'option_value' => $_key,
			'limit'        => 1
		];

		$check = \lib\db\options::get($where);
		if(empty($check))
		{
			return $this->chart_update($_key);
		}
		elseif(isset($check['meta']))
		{
			if(is_string($check['meta']) && substr($check['meta'], 0,1) === '{')
			{
				return json_decode($check['meta'], true);
			}
			elseif(is_array($check['meta']))
			{
				return $check['meta'];
			}
		}
		return [];

	}

	/**
	 * get static number
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function chart_polls()
	{
		$poll_day_chart = $this->chart_get_options('polls');
		$result = [];
		if(is_array($poll_day_chart))
		{
			foreach ($poll_day_chart as $key => $value)
			{
				$date = $key;
				if($this->language === 'fa')
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
		$users_week = $this->chart_get_options('users_week');
		$result = [];
		if(is_array($users_week))
		{
			foreach ($users_week as $key => $value)
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
		$users = $this->chart_get_options('users');
		$result = [];
		if(is_array($users))
		{
			foreach ($users as $key => $value)
			{
				$date = $key;
				if($this->language === 'fa')
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
		$visitor = $this->chart_get_options('visitor');
		$temp     = [];
		foreach ($visitor as $key => $value)
		{
			$date = $key;
			if($this->language === 'fa')
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
		$answer = $this->chart_get_options('answer');
		$result = [];
		if(is_array($answer))
		{
			foreach ($answer as $key => $value)
			{
				$date = $key;
				if($this->language === 'fa')
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
		$transactions = $this->chart_get_options('transactions');
		$result = [];
		if(is_array($transactions))
		{
			foreach ($transactions as $key => $value)
			{
				$date = $key;
				if($this->language === 'fa')
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