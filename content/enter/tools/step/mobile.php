<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait mobile
{
	public function step_mobile($_valid)
	{
		// check valid mobile by status of mobile
		// if this mobile is blocked older
		// check if blocked this mobile
		// check tihs user id by this mobile have a telegram id and start the robot
		// check this user id have a user name
		if($_valid)
		{
			switch ($_valid)
			{
				case 'code':
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
					break;

				case 'pin':
					$this->step = 'pin';
					$this->log('user:verification:use:pin');
					debug::title(T_("Please enter your pin"));
					break;

				case 'telegram':
					if($this->verify_send_telegram())
					{
						// call was send
						$this->step = 'code';
						$this->wait = 1;
						debug::title(T_("A verification code will be sent to your telegram"));
					}
					else
					{

						if($this->verify_call_mobile())
						{
							$this->log('user:verification:cannot:send:telegram:msg');
							// call was send
							$this->step = 'code';
							$this->wait = 5; // wait for 5 seccend to call mobile
							debug::title(T_("A verification code will be sent soon"));
						}
						else
						{
							// this mobile is not a valid mobile
							// check by kavenegar
							$this->log('user:verification:invalid:mobile:after:send:telegram');
							$this->wait = $this->log_sleep_code('invalid:mobile');
							$this->step = 'mobile';
							debug::title(T_("Please enter a valid mobile number"));
						}
					}
					break;

				case 'invalid':
				default:
					$this->step = 'mobile';
					$this->wait = $this->log_sleep_code('invalid:mobile');
					$this->log('user:verification:invalid:mobile');
					debug::title(T_("Please enter a valid mobile number"));
					break;
			}

		}
		else
		{
			$this->step = 'mobile';
			$this->wait = $this->log_sleep_code('invalid:mobile');
			$this->log('user:verification:invalid:mobile');
			debug::title(T_("Please enter a valid mobile number"));
		}
	}
}
?>