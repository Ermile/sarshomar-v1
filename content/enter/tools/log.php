<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait log
{

	/**
	 * save log
	 *
	 * @param      <type>  $_caller  The caller
	 * @param      <type>  $_data    The data
	 */
	public function log($_caller, $_data = null)
	{
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'data'    => $_data,
				'mobile'  => $this->mobile,
				'input'   => utility::post(),
				'session' => $_SESSION,
			]
		];
		\lib\db\logs::set($_caller, null, $log_meta);
	}


	/**
	 * set counter of caller log
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public function counter($_caller, $_block = false)
	{
		if(isset($_SESSION[$_caller]))
		{
			$_SESSION[$_caller]++;
		}
		else
		{
			$_SESSION[$_caller] = 1;
		}

		if($_SESSION[$_caller] > 3 || $_block)
		{
			$_SESSION['enter:user:block'] = true;
		}

		return $_SESSION[$_caller];
	}


	/**
	 * check enter is blocked or no
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function enter_is_blocked()
	{
		if(isset($_SESSION['enter:user:block']) && $_SESSION['enter:user:block'] === true)
		{
			return true;
		}
		return false;
	}


	/**
	 * sleep code for some time
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public function log_sleep_code($_caller = null)
	{
		if($this->enter_is_blocked())
		{
			sleep(7);
			return 7;
		}
		else
		{
			$time = (int) $this->counter($_caller);
			sleep($time);
			return $time;
		}
	}
}
?>