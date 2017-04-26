<?php
namespace lib\utility\answer;
use \lib\db;
use \lib\debug;
use \lib\utility;
use \lib\db\ranks;
use \lib\db\options;
use \lib\utility\users;
use \lib\db\polldetails;
use \lib\utility\profiles;
use \lib\utility\shortURL;
use \lib\utility\stat_polls;

trait money
{

	/**
	 * the user have a money when answer the poll
	 * when less than this munber answered
	 * in money answer time last
	 *
	 * @var        integer
	 */
	public static $money_answer_count = 50;
	public static $money_answer_time  = 60 * 60 * 24;
	private static $money_error = [];

	/**
	 * the user have a money when answer the poll
	 * when less than this munber answered
	 * in money answer time last
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function money($_args)
	{
		// var_dump($_args);exit();
		if(!isset($_args['user_id']) || !isset($_args['poll_id']))
		{
			// array_push(self::$money_error, 'args');
			return false;
		}

		$transactions_search = db\transactions::search("real:answer:poll",
			[
				'user_id'    => $_args['user_id'],
				'post_id'    => $_args['poll_id'],
				'pagenation' => false,
			]
		);

		if(!empty($transactions_search))
		{
			return false;
		}

		// check post privacy
		if(!isset($_args['poll_detail']['privacy']) || $_args['poll_detail']['privacy'] != 'public')
		{
			// array_push(self::$money_error, 'not public');
			return false;
		}

		$user_ask_me = \lib\db\polls::get_user_ask_me_on($_args['user_id']);

		if(intval($_args['poll_id']) !== intval($user_ask_me))
		{
			// array_push(self::$money_error, 'not in ask me');
			return false;
		}

		// check count answer user in last 24 hours
		$first_time = time() - self::$money_answer_time;
		$now        = date("Y-m-d H:i:s");
		$first_time = date("Y-m-d H:i:s", $first_time);

		$query =
		"
			SELECT
				COUNT(DISTINCT polldetails.post_id) AS `count`
			FROM
				polldetails
			WHERE
				polldetails.insertdate >= '$first_time' AND
				polldetails.insertdate < '$now' AND
				polldetails.user_id  = $_args[user_id]
		";
		$count = db::get($query, 'count', true);

		if(intval($count) > self::$money_answer_count)
		{
			// array_push(self::$money_error, 'max 50 poll answer');
			return false;
		}
		// check sarshomr poll
		$sarshomar_poll = false;
		if(isset($_args['poll_detail']['sarshomar']) && $_args['poll_detail']['sarshomar'] === true)
		{
			$sarshomar_poll = true;
		}

		// check filter count
		if(!isset($_args['poll_detail']['filters']['count']) || (isset($_args['poll_detail']['filters']['count']) && !$_args['poll_detail']['filters']['count']))
		{
			// array_push(self::$money_error, 'not count');
			return false;
		}

		$member = $_args['poll_detail']['filters']['count'];

		if(!isset($_args['poll_detail']['count_vote']))
		{
			$count_vote_query =
			"SELECT
				COUNT(DISTINCT polldetails.user_id) AS `count`
			FROM
				polldetails
			WHERE
				polldetails.post_id  = $_args[poll_id]";
			$count_vote = db::get($count_vote_query, 'count', true);
		}
		else
		{
			$count_vote = $_args['poll_detail']['count_vote'];
		}

		if((int) $count_vote >= (int) $member)
		{
			\lib\db\logs::set('answer:money:count_vote:largerthan:member', $_args['user_id'], ['meta' => $_args]);
		}

		// check poll prize
		if(
			!isset($_args['poll_detail']['options']['prize']['value']) ||
			(isset($_args['poll_detail']['options']['prize']['value']) && !$_args['poll_detail']['options']['prize']['value'])
		  )
		{
			if($sarshomar_poll)
			{
				// array_push(self::$money_error, 'is not sarshomr poll');
				return false;
			}
			else
			{
				// 2 sarshomar
				// this poll is public poll and
				db\transactions::set('real:answer:poll:sarshomar', $_args['user_id'], ['debug' => false, 'post_id' => $_args['poll_id']]);
				// array_push(self::$money_error, 'transactions set :) ');
				return true;
			}
		}

		$prize_unit = null;
		$prize_value = $_args['poll_detail']['options']['prize']['value'];
		if(
			!isset($_args['poll_detail']['options']['prize']['unit']) ||
			(isset($_args['poll_detail']['options']['prize']['unit']) && !$_args['poll_detail']['options']['prize']['unit'])
		  )
		{
			\lib\db\logs::set('answer:money:prize:set:unit:not:set', $_args['user_id'], ['meta' => $_args]);
			$prize_unit = 'sarshomar';
		}
		else
		{
			$prize_unit = $_args['poll_detail']['options']['prize']['unit'];
		}

		$units = \lib\db\units::get();
		if(is_array($units))
		{
			$units = array_column($units, 'title');
		}
		else
		{
			$units = [];
		}

		$user_unit = \lib\db\units::user_unit($_args['user_id']);
		if($prize_unit === 'sarshomar' && $user_unit <> 'sarshomar' && $user_unit)
		{
			$prize_value = \lib\db\exchangerates::change_unit_to('sarshomar', $user_unit, $prize_value);
			$prize_unit = $user_unit;
		}

		$caller = "real:answer:poll:$prize_unit";
		if(in_array($prize_unit, $units))
		{
			db\transactions::set($caller, $_args['user_id'], ['debug' => false, 'plus' => $prize_value, 'post_id' => $_args['poll_id']]);
		}
		else
		{
			\lib\db\logs::set('answer:money:error:unit', $_args['user_id'], ['data' => $caller, 'meta' => $_args]);
		}
		return true;
	}


	public static function delete_money($_args)
	{
		if(!isset($_args['user_id']) || !isset($_args['poll_id']))
		{
			return false;
		}

		$transactions_search = db\transactions::search("real:answer:poll",
			[
				'user_id'    => $_args['user_id'],
				'post_id'    => $_args['poll_id'],
				'pagenation' => false,
			]
		);
		if(empty($transactions_search))
		{
			return true;
		}
		elseif(count($transactions_search) === 1)
		{
			$caller = null;
			if(isset($transactions_search[0]['unit']))
			{
				$caller = $transactions_search[0]['unit'];
			}

			$parent_id = null;
			if(isset($transactions_search[0]['id']))
			{
				$parent_id = $transactions_search[0]['id'];
			}

			$minus = false;
			if(isset($transactions_search[0]['plus']))
			{
				$minus = floatval($transactions_search[0]['plus']);
			}
			if($minus)
			{
				db\transactions::set(
					"remove:answer:poll:$caller", $_args['user_id'],
					[
						'minus'     => $minus,
						'post_id'   => $_args['poll_id'],
						'debug'     => false,
						'parent_id' => $parent_id,
					]
				);
			}
		}
		else
		{
			\lib\db\logs::set('answer:money:more:than:one:transaction:found', $_args['user_id'], ['data' => null, 'meta' => $_args]);
		}

	}
}
?>