<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\utility\shortURL;

trait options_members
{
	protected static function insert_options_members()
	{
		// save poll password
		if(self::isset_args('options','members'))
		{
			if(self::$args['options']['members'])
			{
				if(self::check_true_members())
				{
					self::save_options('members', true);
				}
				else
				{
					self::save_options('members', false);
				}
			}
			else
			{
				self::save_options('members', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('members', false);
			}
		}
	}


	/**
	 * check text have true mobile ans signup it
	 * make user list
	 */
	protected static function check_true_members()
	{
		$text = self::$args['options']['members'];
		if(!is_string($text))
		{
			debug::error(T_("Invalid parameter member"),'member', 'arguments');
			return false;
		}

		$split = preg_split("/\n|\,/", $text);
		if(!is_array($split))
		{
			return false;
		}

		$mobiles = [];
		foreach ($split as $key => $value)
		{
			$temp_mobile = \lib\utility\filter::mobile(trim($value));
			if($temp_mobile)
			{
				array_push($mobiles, $temp_mobile);
			}
		}

		if(empty($mobiles))
		{
			return false;
		}

		$query_mobile = "'". implode("','", $mobiles). "'";
		$exist_mobile = "SELECT user_mobile AS `mobile`, id AS `id` FROM users WHERE user_mobile IN ($query_mobile) ";
		$exist_mobile = \lib\db::get($exist_mobile, ['id', 'mobile']);
		// $users_id     = array_keys($exist_mobile);
		$not_signuped = array_diff($mobiles, $exist_mobile);
		$insert_users = [];

		foreach ($not_signuped as $key => $value)
		{
			$insert_users[] =
			[
				'user_mobile' => $value,
				'user_status' => 'awaiting',
				'user_port'   => 'sms',
			];
		}

		// if(!empty($insert_users))
		// {
		// 	$last_insert_id = \lib\db\users::insert_multi($insert_users);
		// 	for ($i =1; $i <= count($insert_users); $i++)
		// 	{
		// 		array_push($users_id, $last_insert_id++);
		// 	}
		// }

		if(!empty($mobiles))
		{

			return self::save_member_group($mobiles);
			// $group_id = self::save_member_group();
			// return self::save_member_list($users_id, $group_id);
		}
		return false;
	}


	/**
	 * Saves a member group in terms
	 */
	protected static function save_member_group($_mobiles)
	{
		$poll_code = shortURL::encode(self::$poll_id);
		$get_options =
		[
			'user_id'      => null,
			'post_id'      => self::$poll_id,
			'option_cat'   => 'poll_'. self::$poll_id,
			'option_key'   => 'member_list',
			'option_value' => 'mobile',
			'limit'        => 1,
		];
		$check = \lib\db\options::get($get_options);

		if(empty($check))
		{
			unset($get_options['limit']);
			$get_options['option_meta'] = json_encode($_mobiles);
			return \lib\db\options::insert($get_options);
		}
		elseif(isset($check['id']))
		{
			return \lib\db\options::update(['option_meta' => json_encode($_mobiles)], $check['id']);
		}
		else
		{
			return false;
		}



		// termusage mode
		// $user_code = shortURL::encode(self::$real_user_id);
		// $get_terms =
		// [
		// 	'term_type' => 'groups',
		// 	'user_id'   => self::$real_user_id,
		// 	'term_slug' => $poll_code,
		// 	'term_url'  => '@/groups/'. $poll_code,
		// 	'limit'     => 1,
		// ];

		// $check = \lib\db\terms::get_multi($get_terms);

		// if(empty($check))
		// {
		// 	unset($get_terms['limit']);
		// 	$get_terms['term_title'] = "group member of ". $poll_code;
		// 	return \lib\db\terms::insert($get_terms);
		// }
		// elseif(isset($check['id']))
		// {
		// 	return (int) $check['id'];
		// }
		// else
		// {
		// 	return false;
		// }
	}


	/**
	 * Saves a member list.
	 * in termusage
	 * @param      <type>  $_user_ids  The users identifiers
	 * @param      <type>  $_group_id   The group identifier
	 */
	protected static function save_member_list($_user_ids, $_group_id)
	{
		// if(!$_group_id)
		// {
		// 	return false;
		// }
		// if(empty($_user_ids))
		// {
		// 	return false;
		// }
		// // disable all other users id
		// \lib\db::query("UPDATE termusages SET termusage_status = 'disable' WHERE term_id = $_group_id AND termusage_foreign = 'group' AND termusage_id NOT IN (". implode(',', $_user_ids). ")");

		// $insert_termusages = [];
		// foreach ($_user_ids as $key => $value)
		// {
		// 	$insert_termusages[] =
		// 	[
		// 		'term_id'           => $_group_id,
		// 		'termusage_id'      => $value,
		// 		'termusage_foreign' => 'group',
		// 	];
		// }

		// if(!empty($insert_termusages))
		// {
		// 	\lib\db\termusages::insert_multi($insert_termusages, ['ignore' => true]);
		// 	return true;
		// }
		// return false;
	}
}
?>