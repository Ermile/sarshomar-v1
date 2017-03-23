<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait pin
{
	public function step_pin()
	{
		if($this->verify_pin_check())
		{
			if($this->verify_call_mobile())
			{
				// call was send
				$this->step = 'code';
				$this->wait = 5;
				debug::title(T_("A verification code will be sent soon"));
			}
			else
			{
				$this->log('user:verification:invalid:mobile');
				$this->wait = $this->log_sleep_code('invalid:mobile');
				// this mobile is not a valid mobile
				// check by kavenegar
				$this->step = 'mobile';
				debug::title(T_("Please enter a valid mobile number"));
			}
		}
		else
		{
			$this->log('user:verification:invalid:pin');
			debug::title(T_("Invalid pin, try again"));

			// this mobile is not a valid mobile
			// check by kavenegar
			$this->wait = $count_log = $this->log_sleep_code('invalid:pin');
			if($count_log >= 5)
			{
				debug::title('<a href="https://sarshomar.com">'. T_("Forgot your pin?") . '</a>');
				$this->step = 'pin';
			}
			elseif($count_log > 3)
			{
				debug::title(T_("In case of entering an invalid pin, you will be blocked"));
				$this->step = 'pin';
			}
			else
			{
				$this->log_sleep_code('invalid:pin');
				$this->step = 'pin';
			}
		}
	}
}
?>