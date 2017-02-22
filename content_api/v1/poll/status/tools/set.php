<?php
namespace content_api\v1\poll\status\tools;
use \lib\utility;
use \lib\debug;

trait set
{
	use check;

	public static $all_status =
	[
		'stop',
		'pause',
		'trash',
		'publish',
		'draft',
		'deleted',
		'awaiting',
		'filtered',
		'blocked',
		'spam',
		'violence',
		'pornography',
		'schedule',
		'expired',
		// 'enable',
		// 'disable',
		// 'other',
	];

	/**
	 * set pollstatus
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_set_status($_options = [])
	{
		debug::title(T_("Can not change status"));

		if(!utility::request("status"))
		{
			return debug::error(T_("Prameter status not set"), 'status', 'arguments');
		}

		$available = self::poll_status();

		if(!debug::$status)
		{
			return ;
		}

		if(isset($available['available']) && !empty($available['available']))
		{
			if(in_array(utility::request("status"), $available['available']))
			{
				$id   = utility\shortURL::decode(utility::request("id"));

				if(utility::request('status') == 'publish')
				{
					self::check(['poll_id' => $id, 'user_id' => $this->user_id]);
				}

				$new_status = utility::request("status");

				if($new_status == 'delete')
				{
					$new_status = 'deleted';
				}

				if(!in_array($new_status, self::$all_status))
				{
					return debug::error(T_("Invalid parameter status"), 'status', 'arguments');
				}

				if(!debug::$status)
				{
					return;
				}

				if(debug::$status === 1)
				{
					debug::title(T_("Poll status changed"));
					$args = ['post_status' => $new_status];
					\lib\db\polls::update($args, $id);
				}
			}
			else
			{
				return debug::error(T_("You can not set this status to this poll, the available status you can change is :available", ['available' => implode(',', $available['available'])]), 'status', 'permission');
			}
		}
		else
		{
			if(in_array(utility::request("status"), self::$all_status))
			{
				return debug::error(T_("You can not set this status to this poll"), 'status', 'permission');
			}
			else
			{
				return debug::error(T_("Invalid parameter status"), 'status', 'arguments');
			}
		}
	}
}
?>