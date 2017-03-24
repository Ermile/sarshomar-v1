<?php
namespace content\enter\sms;


class model extends \mvc\model
{
	/**
	 * [kavenegar_callback description]
	 * @param  [type] $_from      [description]
	 * @param  [type] $_to        [description]
	 * @param  [type] $_message   [description]
	 * @param  [type] $_messageid [description]
	 * @return [type]             [description]
	 */
	public function kavenegar_recieve($_from, $_to, $_message, $_messageid)
	{
		if(!$_from || !$_to)
		{
			return false;
		}
		// we have all data of message after give it

		$user_id = \lib\db\users::signup(
		[
			'mobile'      => $_from,
			'port'        => 'sms',
			// 'password'    => null,
			// 'permission'  => null,
			// 'displayname' => null,
			// 'ref'         => null,
			// 'subport'     => null, // the group code or chanal code
		]);

		$log_meta =
		[
			'data' => $_from,
			'meta' =>
			[
				'from'      => $_from,
				'to'        => $_to,
				'message'   => $_message,
				'messageid' => $_messageid,
			]
		];
		\lib\db\logs::set('sms:recieve', $user_id, $log_meta);
	}


	/**
	 * [kavenegar_delivery description]
	 * @param  [type] $_messageid [description]
	 * @param  [type] $_status    [description]
	 * @return [type]             [description]
	 */
	public function kavenegar_delivery($_messageid, $_status)
	{
		if(!$_messageid)
		{
			return false;
		}
		// show change status data
		$log_meta =
		[
			'data' => $_messageid,
			'meta' =>
			[
				'messageid' => $_messageid,
				'status'    => $_status,
			]
		];
		\lib\db\logs::set('sms:delivery', null, $log_meta);

	}
}
?>