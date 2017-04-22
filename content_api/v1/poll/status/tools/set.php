<?php
namespace content_api\v1\poll\status\tools;
use \lib\utility;
use \lib\debug;

trait set
{
	use check;
	use money;

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

		// \lib\db::transaction();

		$available = self::poll_status(['check_is_my_poll' => true]);
		$current   = null;

		if(isset($available['current']))
		{
			$current = $available['current'];
			\lib\storage::set_current_status($available['current']);
		}

		if(!debug::$status)
		{
			return ;
		}

		if(isset($available['available']) && !empty($available['available']))
		{
			if(in_array(utility::request("status"), $available['available']))
			{
				$id   = utility\shortURL::decode(utility::request("id"));

				$new_status = utility::request("status");

				if($new_status == 'delete')
				{
					$new_status = 'deleted';
				}

				if(!in_array($new_status, self::$all_status))
				{
					return debug::error(T_("Invalid parameter status"), 'status', 'arguments');
				}

				$set_status_on = $new_status;

				if(utility::request('status') == 'publish')
				{
					$set_status_on = self::check(['poll_id' => $id, 'user_id' => $this->user_id, 'status' => $new_status]);
				}

				self::change_dashboard(['old_status' => $current, 'new_status' => $set_status_on, 'user_id' => $this->user_id]);

				if($set_status_on === 'publish')
				{

					// $this->calc_price();
				}

				if(debug::$status)
				{
					debug::title(T_("Poll status changed"));
					$args = ['post_status' => $set_status_on];
					\lib\db\polls::update($args, $id);
					\lib\storage::set_new_status($set_status_on);
					return true;
				}
				return false;
			}
			else
			{
				$T_avalible = [];
				foreach ($available['available'] as $key => $value)
				{
					$T_avalible[] =  T_($value);
				}
				$T_avalible = implode(', ' , $T_avalible);

				return debug::error(T_("You can not set this status to this poll, the available status you can change is :available", ['available' => $T_avalible]), 'status', 'permission');
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


	/**
	 * change user dashboard
	 *
	 * @param      <type>  $_current     The current
	 * @param      <type>  $_new_status  The new status
	 */
	public static function change_dashboard($_options = [])
	{
		$default_options =
		[
			'user_id'    => null,
			'new_status' => null,
			'old_status' => null,
			'poll_type'  => 'poll',
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(!$_options['user_id'])
		{
			return;
		}

		// save offline count of this status only
		$dashboard = ['draft', 'publish', 'awaiting'];
		if(in_array($_options['old_status'], $dashboard))
		{
			\lib\db\userdashboards::minus($_options['user_id'], $_options['old_status']. "_count");
		}

		if(in_array($_options['new_status'], $dashboard))
		{
			\lib\db\userdashboards::plus($_options['user_id'], $_options['new_status']. "_count");
		}

		$stay_in_my_poll = ['draft', 'publish', 'awaiting', 'pause', 'stop', 'expired'];

		if(!in_array($_options['new_status'], $stay_in_my_poll))
		{
			if($_options['poll_type'] === 'poll')
			{
				\lib\db\userdashboards::minus($_options['user_id'], 'my_poll');
			}
			elseif($_options['poll_type'] === 'survey')
			{
				\lib\db\userdashboards::minus($_options['user_id'], 'my_survey');
			}
		}

		if(!in_array($_options['old_status'], $stay_in_my_poll) && in_array($_options['new_status'], $stay_in_my_poll))
		{
			if($_options['poll_type'] === 'poll')
			{
				\lib\db\userdashboards::plus($_options['user_id'], 'my_poll');
			}
			elseif($_options['poll_type'] === 'survey')
			{
				\lib\db\userdashboards::plus($_options['user_id'], 'my_survey');
			}
		}
	}
}
?>