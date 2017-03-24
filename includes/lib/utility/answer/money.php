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


	/**
	 * the user have a money when answer the poll
	 * when less than this munber answered
	 * in money answer time last
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function money_access($_args)
	{
		if(!isset($_args['user_id']) || !isset($_args['poll_id']))
		{
			return false;
		}
		// check post privacy
		if(!isset($_args['poll_detail']['privacy']) || $_args['poll_detail']['privacy'] != 'public')
		{
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
			return false;
		}

		// check filter count
		if(!isset($_args['poll_detail']['filters']['count']))
		{
			return false;
		}

		if(!$_args['poll_detail']['filters']['count'])
		{
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

		// if((int) $count_vote >= (int) $member)
		// {
		// 	return false;
		// }

		// // $count_answered =

		// var_dump($_args);
		// exit();
	}


	public static function money($_args)
	{
		// $money_access = self::money_access($_args);
		// var_dump($count);
		// var_dump($_args);
		// exit();
		// return;
	}
}
?>