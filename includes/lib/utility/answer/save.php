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

trait save
{


	/**
	 * save poll answer
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function save($_args = [])
	{
		// \lib\db::transaction();
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

		/**
		 * save offline chart
		 * in guest mod we needless to save offline chart
		 *
		 * @var        boolean
		 */
		$save_offline_chart = self::user_validataion($_args['user_id']);


		if(!is_array($_args['answer']))
		{
			$_args['answer'] = [$_args['answer']];
		}

		$access = self::access_answer($_args, 'add');

		if(!$access)
		{
			return;
		}

		$user_delete_answer = self::is_answered($_args['user_id'], $_args['poll_id'], ['type' =>  'all']);

		$set_option =
		[
			'answer_txt' => null,
			'validation' => self::$validation,
			'port'       => $_args['port'],
			'subport'    => $_args['subport'],
		];

		$log_meta =
		[
			'meta' =>
			[
				'input'              => $_args,
				'user_delete_answer' => $user_delete_answer,
				'user_validataion'   => self::$validation,
				'user_verify'        => self::$user_verify,
			]
		];

		$skipped = false;

		if($_args['skipped'] == true)
		{
			$skipped = true;
			\lib\db\logs::set('user:answer:skip', $_args['user_id'], $log_meta);
			$result  = polldetails::save($_args['user_id'], $_args['poll_id'], 0, $set_option);
		}
		else
		{
			foreach ($_args['answer'] as $key => $value)
			{
				// save the poll lucked by profile
				// update users profile
				$answers_details =
				[
					'validation'  => self::$validation,
					'user_verify' => self::$user_verify,
					'poll_id'     => $_args['poll_id'],
					'opt_key'     => $key,
					'user_id'     => $_args['user_id'],
					'update_mode' => false,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
				];
				// save user profile if this poll is a profile poll
				profiles::set_profile_by_poll($answers_details);

				$set_option =
				[
					'answer_txt'  => $value,
					'validation'  => self::$validation,
					'user_verify' => self::$user_verify,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
				];

				$result = polldetails::save($_args['user_id'], $_args['poll_id'], $key, $set_option);

				if($save_offline_chart)
				{
					stat_polls::set_poll_result($answers_details);
				}
			}
			\lib\db\logs::set('user:answer:add', $_args['user_id'], $log_meta);
		}

		/**
		 * set count total answere + 1
		 * to get sarshomar total answered
		 * in the minus mode we not change the sarshomar total answered
		 */
		if(!$user_delete_answer)
		{
			stat_polls::set_sarshomar_total_answered();
		}

		// set offline data
		if($skipped)
		{
			if(!users::is_guest($_args['user_id']))
			{
				ranks::plus($_args['poll_id'], 'skip');
			}

			profiles::set_dashboard_data($_args['user_id'], "poll_skipped");
			profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}
		else
		{
			if(!users::is_guest($_args['user_id']))
			{
				// check poll money and set it to the user
				self::money($_args);

				ranks::plus($_args['poll_id'], 'vote');
			}

			profiles::set_dashboard_data($_args['user_id'], "poll_answered");
			profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}

		self::$IS_ANSWERED = [];

		if(\lib\debug::$status)
		{
			return debug::true(T_("Your answer has been submitted"));
		}
		else
		{
			return debug::error(T_("Error in save your answer"));
		}
	}
}
?>