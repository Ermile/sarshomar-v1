<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait call_mobile
{
	public function call_mobile()
	{
		if($this->verify_call_mobile())
		{
			// call was send
			$this->step = 'code';
			$this->wait = 5; // wait 5 seccend to call mobile
			debug::title(T_("A verification code will be sent soon"));
		}
		else
		{
			// this mobile is not a valid mobile
			// check by kavenegar
			$this->log('user:verification:invalid:mobile');
			$this->wait = $this->log_sleep_code('invalid:mobile');
			$this->step = 'mobile';
			debug::title(T_("Please enter a valid mobile number"));
		}
	}
}
?>