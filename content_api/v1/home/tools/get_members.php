<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_members
{
	private static function get_members(&$_poll_data)
	{
		$user_id = self::$private_user_id;
		$post_id = self::$private_poll_id;
		if(!$user_id)
		{
			return false;
		}
		if(!$post_id)
		{
			return false;
		}

		$get_options =
		[
			'user_id'      => null,
			'post_id'      => $post_id,
			'option_cat'   => 'poll_'. $post_id,
			'option_key'   => 'member_list',
			'option_value' => 'mobile',
			'limit'        => 1,
		];
		$check = \lib\db\options::get($get_options);
		if(!empty($check) && isset($check['meta']))
		{
			if(self::$private_is_my_poll)
			{
			 	$_poll_data['options']['members'] = implode("\n", json_decode($check['meta'], true));
			}
		}

		// $get_terms =
		// [
		// 	'term_type' => 'groups',
		// 	'user_id'   => $user_id,
		// 	'term_slug' => shortURL::encode($post_id),
		// 	'term_url'  => '@/groups/'. shortURL::encode($post_id),
		// 	'limit'     => 1,
		// ];

		// $check = \lib\db\terms::get_multi($get_terms);

		// if(!empty($check) && isset($check['id']))
		// {
		// 	if(self::$private_is_my_poll)
		// 	{
		// 		$query =
		// 		"SELECT user_mobile AS `mobile`
		// 	   	 FROM users
		// 	   	 WHERE users.id IN
		// 	   	 (SELECT termusage_id
		// 	   	 FROM termusages
		// 	   	 WHERE termusage_foreign = 'group'
		// 	   	 AND term_id = $check[id]
		// 	   	 AND termusage_status = 'enable') ";
		// 	   	$mobiles = \lib\db::get($query, 'mobile');
		// 	   	if(!empty($mobiles))
		// 	   	{
		// 	   		$_poll_data['options']['members'] = implode("\n", $mobiles);
		// 	   	}
		// 	}
		// }
	}
}
?>