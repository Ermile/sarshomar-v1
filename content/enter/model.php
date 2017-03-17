<?php
namespace content\enter;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \mvc\model
{

	public $mobile    = null;
	public $username  = null;
	public $pin       = null;
	public $code      = null;
	public $user_data = [];
	public $user_id   = null;


	use tools\check;
	use tools\log;
	use tools\login;
	use tools\signup;
	use tools\verify;

	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_enter($_args)
	{
		if($this->login())
		{
			$this->redirector('@')->redirect();
			return;
		}

		if($this->login_by_remember())
		{
			return;
		}

		if($this->check_is_bot())
		{
			\lib\error::access(T_("You are bot"));
		}

	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_enter($_args)
	{
		// if the user was login redirect to @ page
		if($this->login())
		{
			$this->redirector('@')->redirect();
			return;
		}

		if($this->login_by_remember())
		{
			return;
		}

		// check the user is block
		if($this->enter_is_blocked())
		{
			$this->log('use:enter:blocked:agent:ip');
			$this->log_sleep_code('use:enter:blocked:agent:ip');
			debug::msg('step', 'block');
			debug::error(T_("You are blocked"));
			return false;
		}

		$ok   = 'false';
		$step = 'mobile';
		// check input and get the step
		$check_input = $this->check_input();

		// load data of users by search mobile
		if($check_input)
		{
			$mobile          = utility::post('mobile');
			// $this->mobile    = utility\filter::mobile($mobile);
			$this->mobile    = $mobile;
			$this->user_data = \lib\db\users::get_by_mobile(utility\filter::mobile($this->mobile));
			if(isset($this->user_data['id']))
			{
				$this->user_id = $this->user_data['id'];
			}
		}

		switch ($check_input)
		{
			// input in step1
			case 'step1':
				// check valid mobile by status of mobile
				// if this mobile is blocked older
				$valid = $this->check_valid_mobile_username();
				if($valid)
				{
					switch ($valid)
					{
						case 'code':
							if($this->verify_call_mobile())
							{
								// call was send
								$ok   = 'true';
								$step = 'code';
								debug::title(T_("1.We send verification code nearly"));
							}
							else
							{
								$this->log('user:verification:invalid:mobile');
								$this->log_sleep_code('invalid:mobile');
								// this mobile is not a valid mobile
								// check by kavenegar
								$ok   = 'false';
								$step = 'mobile';
								debug::title(T_("2.Please set a valid mobile number"));
							}
							break;

						case 'pin':
							$ok   = 'true';
							$step = 'pin';
							$this->log('user:verification:use:pin');
							debug::title(T_("3.Please enter your pin"));
							break;

						case 'invalid':
						default:
							$ok   = 'false';
							$step = 'mobile';
							$this->log('user:verification:invalid:mobile');
							$this->log_sleep_code('invalid:mobile');
							debug::title(T_("4.Please set a valid mobile number"));
							break;
					}
					// call mobile
				}
				else
				{
					$ok   = 'false';
					$step = 'mobile';
					$this->log('user:verification:invalid:mobile');
					$this->log_sleep_code('invalid:mobile');
					debug::title(T_("5.Please set a valid mobile number"));
				}
				break;

			// system in step 3
			case 'step2':
				if($this->verify_pin_check())
				{
					if($this->verify_call_mobile())
					{
						// call was send
						$ok   = 'true';
						$step = 'code';
						debug::title(T_("6.We send verification code nearly"));
					}
					else
					{
						$this->log('user:verification:invalid:mobile');
						$this->log_sleep_code('invalid:mobile');
						// this mobile is not a valid mobile
						// check by kavenegar
						$ok   = 'false';
						$step = 'mobile';
						debug::title(T_("7.Please set a valid mobile number"));
					}
				}
				else
				{
					$this->log('user:verification:invalid:pin');
					$this->log_sleep_code('invalid:mobile');
					// this mobile is not a valid mobile
					// check by kavenegar
					$ok   = 'false';
					if($this->counter('invalid:code') > 3)
					{
						$step = 'mobile';
					}
					else
					{
						$step = 'pin';
					}
					debug::title(T_("8.Please set a valid mobile number"));
				}
				break;
			// system in step 2
			// we check the pin code
			case 'step3':
				if($this->verify_check())
				{
					// the verification code is true
					// set login
					debug::title(T_("9.Login successfuly"));
					$this->login_set();
					$ok   = 'true';
					$step = 'login';
				}
				else
				{
					debug::title(T_("10.Invalid verfication code"));
					$this->log('user:verfication:invalid:code');
					$this->log_sleep_code('invalid:code');
					$ok = 'false';
					if($this->counter('invalid:code') > 3)
					{
						$step = 'mobile';
					}
					else
					{
						$step = 'code';
					}
				}
				break;

			default:
				debug::title(T_("11.Invalid verfication code"));
				$this->log('user:verfication:invalid:input');
				$this->counter('user:verfication:invalid:input');
				// invalid input
				$step = 'mobile';
				$ok   = 'false';
				break;
		}

		debug::msg('ok', $ok);
		debug::msg('step', $step);
	}
}
?>