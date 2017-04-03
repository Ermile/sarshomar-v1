<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait mobile
{
	public function step_mobile($_way)
	{
		// check valid mobile by status of mobile
		// if this mobile is blocked older
		// check if blocked this mobile
		// check tihs user id by this mobile have a telegram id and start the robot
		// check this user id have a user name
		if($_way)
		{
			switch ($_way)
			{
				case 'code':
				case 'call':
					$this->call_mobile();
					break;

				case 'sms1':
					$this->send_sms();
					$this->step = 'code';
					$this->wait = 5; // wait 5 seccend to call mobile
					break;

				case 'pin':
					// need less to send eny thing
					// we wait for user set he pin
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
						$this->log('user:verification:cannot:send:telegram:msg');
						$this->call_mobile();
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