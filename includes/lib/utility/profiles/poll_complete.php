<?php
namespace lib\utility\profiles;

trait poll_complete
{

	/**
	 * to save profile value by answered the poll
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set_profile_by_poll($_args)
	{
		if(
			!isset($_args['poll_id']) ||
			!isset($_args['opt_key']) ||
			!isset($_args['user_id'])
		  )
		{
			return false;
		}


		$profile_lock =
		"
			SELECT
				terms.*
			FROM
				termusages
			INNER JOIN terms ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = 'profile' AND
				termusages.termusage_id =
				(
					SELECT
						pollopts.id
					FROM
						pollopts
					WHERE
						pollopts.post_id = $_args[poll_id] AND
						pollopts.key = $_args[opt_key]
					LIMIT 1
				)
			-- profiles::set_profile_by_poll()
		";

		// check this poll has been locked to profile data ?
		$profile_lock = \lib\db::get($profile_lock);

		if(!$profile_lock || empty($profile_lock) || !is_array($profile_lock))
		{
			return false;
		}

		foreach ($profile_lock as $key => $value)
		{
			if(!isset($value['id']) || !isset($value['term_caller']) ||	(isset($value['term_caller']) && !$value['term_caller']))
			{
				continue;
			}

			if(isset($_args['type']) && $_args['type'] === 'minus')
			{
				$deactive_term_usages =
				"
					INSERT INTO
						termusages
					SET
						termusages.termusage_foreign = 'users',
						termusages.termusage_id      = $_args[user_id],
						termusages.term_id           = $value[id]
					ON DUPLICATE KEY UPDATE
						termusages.term_id     = $value[id],
						termusages.termusage_status = 'disable'
					-- profiles::set_profile_by_poll() >> deactive old termusage
				";
				\lib\db::query($deactive_term_usages);
			}
			else
			{
				$insert_user_termusages =
				"
					INSERT INTO
						termusages
					SET
						termusages.termusage_foreign = 'users',
						termusages.termusage_id      = $_args[user_id],
						termusages.term_id           = $value[id]
					ON DUPLICATE KEY UPDATE
						termusages.term_id = $value[id]
					-- profiles::set_profile_by_poll() >> set new termuseage

				";
				\lib\db::query($insert_user_termusages);
			}

			if(!preg_match("/\:/", $value['term_caller']))
			{
				continue;
			}

			$split = explode(':', $value['term_caller']);
			if(isset($split[0]) && isset($split[1]))
			{
				if(!\lib\db\filters::support_filter($split[0], $split[1]))
				{
					continue;
				}
			}
			else
			{
				continue;
			}
			self::set_profile_data($_args['user_id'], [$split[0] => $split[1]], $_args);
		}
	}


	/**
	 * save users change profile in log table
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function save_change_log($_args)
	{
		if(
			!isset($_args['user_id'])    ||
			!isset($_args['key'])		 ||
			!isset($_args['old_value'])  ||
			!isset($_args['new_value'])
		  )
		{
			return false;
		}

		$caller      = "change_$_args[key]";
		$log_item_id = \lib\db\logitems::get_id($caller);
		if(!$log_item_id)
		{
			// list of priority in log item table
			// 'critical','high','medium','low'
			$log_item_priority = null;

			switch ($_args['key'])
			{
				case 'gender':
					$log_item_priority = 'critical';
					break;

				default:
					$log_item_priority = 'high';
					break;
			}

			$insert_log_item =
			[
				'logitem_type'     => 'users',
				'logitem_caller'   => $caller,
				'logitem_title'    => $caller,
				'logitem_desc'     => $caller,
				'logitem_meta'     => null,
				'logitem_priority' => $log_item_priority,
			];
			$log_item_id = \lib\db\logitems::insert($insert_log_item);
			$log_item_id = \lib\db::insert_id();
		}

		if(!$log_item_id)
		{
			return false;
		}

		$insert_log =
		[
			'logitem_id'     => $log_item_id,
			'user_id'        => $_args['user_id'],
			'log_data'       => $_args['key'],
			'log_meta'       => "{\"old\":\"$_args[old_value]\",\"new\":\"$_args[new_value]\"}",
			'log_createdate' => date("Y-m-d H:i:s")
		];
		\lib\db\logs::insert($insert_log);
	}
}
?>