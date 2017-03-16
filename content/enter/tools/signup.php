<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait signup
{
	public function signup($_type = null)
	{
		if($_type === 'block')
		{
			$signup =
			[
				'mobile'     => $this->mobile,
				'password'   => \lib\utility::hasher(time(). '!~~!'. rand(10000,99999)),
				'permission' => null,
				'port'       => 'site'
			];
			$user_id  = \lib\db\users::signup($signup);

			if($user_id)
			{
				\lib\db\users::update(['user_status' => 'block'], $user_id);
			}
			return false;
		}

		$signup =
		[
			'mobile'     => $this->mobile,
			'password'   => \lib\utility::hasher(time(). '!~~!'. rand(10000,99999)),
			'permission' => null,
			'port'       => 'site'
		];

		$user_id  = \lib\db\users::signup($signup);
		return $user_id;
	}
}
?>