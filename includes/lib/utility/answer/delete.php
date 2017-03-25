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

trait delete
{


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function delete($_args)
	{
		// \lib\db::transaction();
		$default_args =
		[
			'user_id'    => null,
			'poll_id'    => null,
			'old_answer' => [],
		];

		if(!is_array($_args))
		{
			return false;
		}
		$_args = array_merge($default_args, $_args);

		$old_answer = polldetails::get($_args['user_id'], $_args['poll_id']);

		if(!is_array($old_answer))
		{
			$old_answer = [];
		}

		$old_answer_is_skipped = false;
		foreach ($_args['old_answer'] as $key => $value)
		{
			if(isset($value['key']))
			{
				if($value['key'] === 0)
				{
					$old_answer_is_skipped = true;
				}
			}
			else
			{
				continue;
			}
			self::$validation = 'invalid';
			$profile          = 0;
			$user_verify      = null;

			foreach ($old_answer as $k => $v)
			{
				if(isset($v['opt']) && $v['opt'] == $value['key'])
				{
					if(isset($v['validstatus']))
					{
						self::$validation = $v['validstatus'];
					}
					if(isset($v['profile']))
					{
						$profile = $v['profile'];
					}
				}

				if(array_key_exists('validstatus', $v))
				{
					$user_verify = $v['validstatus'];
				}
			}

			$answers_details =
			[
				'poll_id'     => $_args['poll_id'],
				'opt_key'     => $value['key'],
				'user_id'     => $_args['user_id'],
				'type'        => 'minus',
				'update_mode' => 'delete',
				'profile'     => $profile,
				'validation'  => self::$validation,
				'user_verify' => $user_verify,
			];
			stat_polls::set_poll_result($answers_details);
		}


		$log_meta =
		[
			'meta' =>
			[
				'input'              => $_args,
			]
		];


		self::$IS_ANSWERED = [];

		$result = polldetails::remove($_args['user_id'], $_args['poll_id']);

		if($result && db::affected_rows())
		{
			if($old_answer_is_skipped)
			{
				// transactions::set('real:answer:poll', $_args['user_id'], ['plus' => 2, 'post_id' => $_args['post_id']]);

				if(!users::is_guest($_args['user_id']))
				{
					ranks::minus($_args['poll_id'], 'skip');
				}

				profiles::minus_dashboard_data($_args['user_id'], "poll_skipped");
				profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
			}
			else
			{
				if(!users::is_guest($_args['user_id']))
				{
					ranks::minus($_args['poll_id'], 'vote');
				}

				profiles::minus_dashboard_data($_args['user_id'], "poll_answered");
				profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
			}

			\lib\db\logs::set('user:answer:delete', $_args['user_id'], $log_meta);
			self::delete_money($_args);
			return debug::title(T_("Your answer has been deleted"));
		}
		else
		{
			\lib\db\logs::set('user:answer:delete:error', $_args['user_id'], $log_meta);
			return debug::error(T_("You have not answered to this poll"));
		}
	}
}
?>