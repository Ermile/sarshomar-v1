<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait code
{
	public function step_code()
	{
		if($this->verify_check())
		{
			// the verification code is true
			// set login
			debug::title(T_("Logged in successfully"));
			$this->login_set();
			$this->step = 'login';
		}
		else
		{
			$this->log('user:verfication:invalid:code');
			$this->wait = $count_log = $this->log_sleep_code('invalid:code');
			if($count_log >= 5)
			{
				debug::title(T_("You are trying to cheat us!"));
				$this->step = 'block';

			}
			elseif($count_log > 3)
			{
				debug::title(T_("Too many wrong inputs! Be Careful."));
				$this->step = 'code';
			}
			else
			{
				debug::title(T_("Invalid verfication code"));
				$this->step = 'code';
			}
		}
	}
}
?>