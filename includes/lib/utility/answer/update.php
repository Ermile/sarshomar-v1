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

trait update
{




	/**
	 * update the user answer
	 *
	 * @param      <type>  $_args['user_id']  The user identifier
	 * @param      <type>  $_args['poll_id']  The poll identifier
	 * @param      <type>  $_args['answer']   The answer
	 * @param      array   $_option   The option
	 */
	public static function update($_args = [])
	{
		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
		];
		$_args = array_merge($default_args, $_args);

		// check old answer and new answer and remove some record if need or insert record if need
		// or the old answer = new answer the return true
		// or old answer is skipped and new is a opt must be update
		// or old answer is a opt and now the user skipped the poll

		// when update the polldetails neet to update the pollstats
		// on this process we check the old answer and new answer
		// and update pollstats if need

		if(!self::access_answer($_args, 'edit'))
		{
			return;
		}

		$old_opt = [];

		self::$old_answer = polldetails::get($_args['user_id'], $_args['poll_id']);

		if(is_array(self::$old_answer))
		{
			$old_opt =  array_column(self::$old_answer, 'opt');
		}

		$new_opt = array_keys($_args['answer']);

		// check updated chart to update poll ranks
		$update_chart_old = false;
		$update_chart_new = false;

		$log_meta =
		[
			'meta' =>
			[
				'input'            => $_args,
				'old_answer'       => self::$old_answer,
				'user_validataion' => self::$validation,
				'user_verify'      => self::$user_verify,
			]
		];

		if($old_opt == $new_opt && !$_args['skipped'])
		{

			\lib\db\logs::set('user:answer:update:error:duplicate', $_args['user_id'], $log_meta);
			debug::error(T_("You have already selected this answer and submited"), 'answer', 'permission');
			return false;
		}

		$save_offline_chart = self::user_validataion($_args['user_id']);

		$log_meta['meta']['must_remove'] = self::$must_remove = array_diff($old_opt, $_args['answer']);
		$log_meta['meta']['must_insert'] = self::$must_insert = array_diff($_args['answer'], $old_opt);

		$old_answer_is_skipped = false;
		$new_answer_is_skipped = false;

		// remove answer must be remove
		foreach (self::$must_remove as $key => $value)
		{
			if($value === '0')
			{
				$old_answer_is_skipped = true;
			}

			$remove_old_answer = polldetails::remove($_args['user_id'], $_args['poll_id'], $value);

			$profile          = 0;
			self::$validation = 'invalid';
			$user_verify      = null;

			foreach (self::$old_answer as $i => $o)
			{
				if($o['opt'] == $value)
				{

					$profile          = isset($o['profile']) ? $o['profile'] : null;
					self::$validation = isset($o['validstatus']) ? $o['validstatus'] : null;
				}

				if(array_key_exists('validstatus', $o))
				{
					$user_verify = $o['validstatus'];
				}
			}

			$answers_details =
			[
				'poll_id'     => $_args['poll_id'],
				'opt_key'     => $value,
				'user_id'     => $_args['user_id'],
				'type'        => 'minus',
				'profile'     => $profile,
				'user_verify' => $user_verify,
				'validation'  => self::$validation,
				'port'        => $_args['port'],
				'subport'     => $_args['subport'],
			];
			// unset user profile if this poll is profile poll
			profiles::set_profile_by_poll($answers_details);

			if($save_offline_chart)
			{
				$temp_update_chart = stat_polls::set_poll_result($answers_details);
				if(!$update_chart_old && $temp_update_chart)
				{
					$update_chart_old = true;
				}
			}
		}

		$set_option =
		[
			'answer_txt'  => null,
			'validation'  => self::$validation,
			'port'        => $_args['port'],
			'subport'     => $_args['subport'],
			'user_verify' => self::$user_verify,
		];

		if($_args['skipped'] === true)
		{
			$new_answer_is_skipped = true;
			$result  = polldetails::save($_args['user_id'], $_args['poll_id'], 0, $set_option);
			profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}
		elseif(!empty(self::$must_insert))
		{
			foreach ($_args['answer'] as $key => $value)
			{
				// update users profile
				$answers_details =
				[
					'validation'  => self::$validation,
					'poll_id'     => $_args['poll_id'],
					'opt_key'     => $key,
					'user_id'     => $_args['user_id'],
					'update_mode' => true,
					'user_verify' => self::$user_verify,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],

				];
				// set user profile if this poll is profile poll
				profiles::set_profile_by_poll($answers_details);

				$set_option =
				[
					'answer_txt'  => $value,
					'validation'  => self::$validation,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
					'user_verify' => self::$user_verify,
				];

				$result = polldetails::save($_args['user_id'], $_args['poll_id'], $key, $set_option);
				// save the poll lucked by profile

				if($save_offline_chart)
				{
					$temp_update_chart_new = stat_polls::set_poll_result($answers_details);
					if(!$update_chart_new && $temp_update_chart_new)
					{
						$update_chart_new = true;
					}
				}
			}
			profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}

		\lib\db\logs::set('user:answer:update', $_args['user_id'], $log_meta);
		// plus answer update count
		$where =
		[
			'post_id'      => (int) $_args['poll_id'],
			'user_id'      => (int) $_args['user_id'],
			'option_cat'   => "user_detail_$_args[user_id]",
			'option_key'   => "update_answer_$_args[poll_id]",
			'option_value' => "update_answer",
		];

		options::plus($where);

		self::$IS_ANSWERED = [];

		// if($old_answer_is_skipped && $new_answer_is_skipped) || (!$old_answer_is_skipped && !$new_answer_is_skipped)
		// nothing
		// needless to update offline data
		// in dashboard and post rank

		if($old_answer_is_skipped && !$new_answer_is_skipped)
		{
			if($save_offline_chart)
			{
				if($update_chart_new)
				{
					ranks::plus($_args['poll_id'], 'vote');
				}
				ranks::minus($_args['poll_id'], 'skip');
			}

			profiles::minus_dashboard_data($_args['user_id'], "poll_skipped");
			profiles::people_see_my_poll($_args['poll_id'], "skipped", 'minus');

			profiles::set_dashboard_data($_args['user_id'], "poll_answered");
			profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}

		if(!$old_answer_is_skipped && $new_answer_is_skipped)
		{
			if($save_offline_chart)
			{
				if($update_chart_old)
				{
					ranks::minus($_args['poll_id'], 'vote');
				}

				ranks::plus($_args['poll_id'], 'skip');
			}

			profiles::minus_dashboard_data($_args['user_id'], "poll_answered");
			profiles::people_see_my_poll($_args['poll_id'], "answered", 'minus');

			profiles::set_dashboard_data($_args['user_id'], "poll_skipped");
			profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}
		$text = null;
		if(isset($_args['answer']) && is_array($_args['answer']))
		{
			$text = implode(T_(',') . ' ', $_args['answer']);
		}

		return debug::true(T_("Your answer updated to <b> :text </b>", ['text' => $text]));
		// return debug::true(T_("Your answer updated"));
	}
}
?>