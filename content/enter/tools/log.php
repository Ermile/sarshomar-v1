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
			'data' => $_data,
			'meta' =>
			[
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

		if($_SESSION[$_caller] > 10 || $_block)
		{
			if($this->block_type === 'ip-agent')
			{
				$log_meta =
				[
					'data' => ClientIP . '_'. utility\visitor::get('agent'),
					'meta' =>
					[
						'mobile'  => $this->mobile,
						'input'   => utility::post(),
						'session' => $_SESSION,
					],
				];
				db\logs::set('ip:agent:block', $this->user_id, $log_meta);
				// block ip agent in log
			}

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
		if($this->block_type === 'ip-agent')
		{
			$caller = db\logitems::caller('ip:agent:block');
			$where =
			[
				'logitem_id' => $caller,
				'log_data'   => ClientIP . '_'. utility\visitor::get('agent'),
				'log_status' => 'enable',
				'limit'      => 1
			];
			$is_blocked = db\logs::get($where);
			if(!empty($is_blocked))
			{
				return true;
			}
		}

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