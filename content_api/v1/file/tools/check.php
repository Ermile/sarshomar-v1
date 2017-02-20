<?php
namespace content_api\v1\file\tools;

trait check
{
	/**
	 * user uploaded size
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function uploaded($_user_id)
	{
		$where =
		[
			'user_id'    => $_user_id,
			'post_id'    => null,
			'option_cat' => 'user_detail_'. $_user_id,
			'option_key' => 'user_uploaded_size',
			'limit'      => 1,
		];
		$result =  \lib\db\options::get($where);
		if(isset($result['value']))
		{
			return (int) $result['value'];
		}
		return 0;
	}


	/**
	 * user vip size
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	private static function vip_size($_user_id)
	{
		$where =
		[
			'user_id'    => $_user_id,
			'post_id'    => null,
			'option_cat' => 'user_vip_size_'. $_user_id,
			'option_key' => 'user_uploaded_size',
			'limit'      => 1,
		];
		$result =  \lib\db\options::get($where);
		if(isset($result['value']))
		{
			return (int) $result['value'];
		}
		return false;
	}


	/**
	 * check remaining user size
	 */
	public static function remaining($_user_id)
	{
		$default_user_size = 1000000; // 1 MB
		$uploaded          = self::uploaded($_user_id);
		$vip_size          = self::vip_size($_user_id);
		if(is_int($vip_size))
		{
			$default_user_size = $vip_size;
		}
		return $default_user_size - $uploaded;
	}


	/**
	 * plus the user size
	 */
	public static function user_size_plus($_user_id, $_size)
	{
		$where =
		[
			'user_id'    => $_user_id,
			'post_id'    => null,
			'option_cat' => 'user_detail_'. $_user_id,
			'option_key' => 'user_uploaded_size',
		];
		\lib\db\options::plus($where, (int) $_size);
	}

}
?>