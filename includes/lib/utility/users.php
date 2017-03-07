<?php
namespace lib\utility;

class users
{
	/**
	 * the ports in data base
	 *
	 * @var        array
	 */
	public static $user_port =
	[
		'site', 'api', 'guest', 'android', 'telegram',
		'instagram', 'google', 'linkedin', 'github',
		'facebook', 'twitter', 'other',	'ios', 'wp',
	];


	/**
	 * the user trust in database
	 *
	 * @var        array
	 */
	public static $user_trust = ['valid', 'invalid', 'unknown'];


	/**
	 * the user verify in database
	 *
	 * @var        array
	 */
	public static $user_verify = ['mobile', 'complete', 'unknown', 'uniqueid'];


	/**
	 * users status in database
	 *
	 * @var        array
	 */
	public static $user_status = ['active', 'awaiting', 'deactive', 'removed', 'filter', 'spam', 'block', 'delete'];


	/**
	 * { function_description }
	 */
	public static function signup($_args = [])
	{
		$default_args =
		[
			'mobile'      => null,
			'password'    => null,
			'permission'  => null,
			'displayname' => null,
			'ref'         => null,
			'type'        => null,
			'port'        => null,
			'subport'     => null,
			'insert_id'   => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['insert_id'])
		{
			return false;
		}

		$user_update = [];

		if($_args['port'] && in_array($_args['port'], self::$user_port))
		{
			$user_update['user_port'] = $_args['port'];
		}

		if($_args['type'] === 'inspection')
		{
			$user_update['user_status'] = 'deactive';
		}

		if(!empty($user_update))
		{
			\lib\db\users::update($user_update, $_args['insert_id']);
		}
	}


	/**
	 * verify users
	 *
	 * @param      array  $_args  The arguments
	 */
	public static function verify($_args = [])
	{
		$default_args =
		[
			'mobile'      => null,
			'ref'         => null,
			'type'        => null,
			'port'        => null,
			'subport'     => null,
			'user_id'     => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['user_id'])
		{
			return false;
		}

		$user_update = [];

		if($_args['mobile'])
		{
			$user_update['user_verify'] = 'mobile';
		}

		if(!empty($user_update))
		{
			\lib\db\users::update($user_update, $_args['user_id']);
		}

	}
}
?>