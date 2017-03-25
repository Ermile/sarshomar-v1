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
		// singup is locked
		if(!$this->signup)
		{
			// the user set user name cat not signup by username
			return false;
		}

		if($_type === 'block')
		{
			$signup =
			[
				'mobile'     => $this->mobile,
				'password'   => null,
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
			'password'   => null,
			'permission' => null,
			'port'       => 'site'
		];

		if(!$this->is_guest)
		{
			$_SESSION['first_signup'] = true;
		}

		$user_id  = \lib\db\users::signup($signup);

		return $user_id;
	}
}
?>