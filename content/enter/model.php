<?php
namespace content\enter;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \mvc\model
{

	public $mobile           = null;
	public $username         = null;
	public $pin              = null;
	public $code             = null;
	public $user_data        = [];
	public $user_id          = null;
	public $signup           = false;
	public $telegram_chat_id = null;
	public $telegram_detail  = [];
	// public $block_type    = 'ip-agent';
	public $block_type       = 'session';
	public $is_guest         = false;

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
		if($this->login() && \lib\utility\users::is_guest($this->login('id')))
		{
			// $this->is_guest = true;
			$this->is_guest = false;
		}

		if($this->login())
		{
			if(!$this->is_guest)
			{
				$this->redirector('@')->redirect();
				return;
			}
		}

		if($this->login_by_remember())
		{
			if(!$this->is_guest)
			{
				return;
			}
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
		if($this->login() && \lib\utility\users::is_guest($this->login('id')))
		{
			$this->is_guest = true;
		}

		// if the user was login redirect to @ page
		if($this->login())
		{
			if(!$this->is_guest)
			{
				$this->redirector('@')->redirect();
				return;
			}
		}

		if($this->login_by_remember())
		{
			if(!$this->is_guest)
			{
				return;
			}
		}

		// check the user is block
		if($this->enter_is_blocked())
		{
			$this->log('use:enter:blocked:agent:ip');
			$this->log_sleep_code('use:enter:blocked:agent:ip');
			debug::msg('step', 'block');
			debug::msg('wait', 10);
			debug::title(T_("You are blocked"));
			return false;
		}
		// step
		$step = 'mobile';
		// wait for 0 seccend
		$wait = 0;
		// check input and get the step
		$check_input = $this->check_input();

		// load data of users by search mobile
		if($check_input)
		{
			$mobile = utility::post('mobile');
			if(ctype_digit($mobile))
			{
				$mobile          = utility\filter::mobile($mobile);
				$this->mobile    = $mobile;
				$this->user_data = \ilib\db\users::get_by_mobile(utility\filter::mobile($this->mobile));
				$this->signup    = true;

				if(isset($this->user_data['id']))
				{
					$this->signup  = false;
					$this->user_id = $this->user_data['id'];
				}
			}
			else
			{
				$this->signup    = false;
				$this->user_data = \ilib\db\users::get_by_username($mobile);
				if(
					isset($this->user_data['id']) &&
					isset($this->user_data['user_mobile']) &&
					isset($this->user_data['user_pass']) &&
					isset($this->user_data['user_status']) &&
					$this->user_data['user_status'] === 'active'
				 )
				{
					$this->signup  = false;
					$this->mobile  = $this->user_data['user_mobile'];
					$this->user_id = $this->user_data['id'];

				}
				else
				{
					$this->log('use:enter:username:notexist');
					$this->log_sleep_code('use:enter:username:notexist');
					debug::msg('wait', 10);
					debug::msg('step', 'mobile');
					debug::title(T_("Invalid username"));
					return false;
				}
			}
		}

		switch ($check_input)
		{
			// input in mobile
			case 'mobile':
				// check valid mobile by status of mobile
				// if this mobile is blocked older
				// check if blocked this mobile
				// check tihs user id by this mobile have a telegram id and start the robot
				// check this user id have a user name
				$valid = $this->check_valid_mobile_username();
				if($valid)
				{
					switch ($valid)
					{
						case 'code':
							if($this->verify_call_mobile())
							{
								// call was send
								$step = 'code';
								$wait = 5; // wait 5 seccend to call mobile
								debug::title(T_("A verification code will be sent soon"));
							}
							else
							{
								// this mobile is not a valid mobile
								// check by kavenegar
								$this->log('user:verification:invalid:mobile');
								$wait = $this->log_sleep_code('invalid:mobile');
								$step = 'mobile';
								debug::title(T_("Please enter a valid mobile number"));
							}
							break;

						case 'pin':
							$step = 'pin';
							$this->log('user:verification:use:pin');
							debug::title(T_("Please enter your pin"));
							break;

						case 'telegram':
							if($this->verify_send_telegram())
							{
								// call was send
								$step = 'code';
								$wait = 1;
								debug::title(T_("A verification code will be sent to your telegram"));
							}
							else
							{
								if($this->verify_call_mobile())
								{
									$this->log('user:verification:cannot:send:telegram:msg');
									// call was send
									$step = 'code';
									$wait = 5; // wait for 5 seccend to call mobile
									debug::title(T_("A verification code will be sent soon"));
								}
								else
								{
									// this mobile is not a valid mobile
									// check by kavenegar
									$this->log('user:verification:invalid:mobile:after:send:telegram');
									$wait = $this->log_sleep_code('invalid:mobile');
									$step = 'mobile';
									debug::title(T_("Please enter a valid mobile number"));
								}
							}
							break;

						case 'invalid':
						default:
							$step = 'mobile';
							$wait = $this->log_sleep_code('invalid:mobile');
							$this->log('user:verification:invalid:mobile');
							debug::title(T_("Please enter a valid mobile number"));
							break;
					}
					// call mobile
				}
				else
				{
					$step = 'mobile';
					$wait = $this->log_sleep_code('invalid:mobile');
					$this->log('user:verification:invalid:mobile');
					debug::title(T_("Please enter a valid mobile number"));
				}
				break;

			// system in step 3
			case 'pin':
				if($this->verify_pin_check())
				{
					if($this->verify_call_mobile())
					{
						// call was send
						$step = 'code';
						$wait = 5;
						debug::title(T_("A verification code will be sent soon"));
					}
					else
					{
						$this->log('user:verification:invalid:mobile');
						$wait = $this->log_sleep_code('invalid:mobile');
						// this mobile is not a valid mobile
						// check by kavenegar
						$step = 'mobile';
						debug::title(T_("Please enter a valid mobile number"));
					}
				}
				else
				{
					$this->log('user:verification:invalid:pin');
					debug::title(T_("Invalid pin, try again"));

					// this mobile is not a valid mobile
					// check by kavenegar
					$wait = $count_log = $this->log_sleep_code('invalid:pin');
					if($count_log >= 5)
					{
						debug::title('<a href="https://sarshomar.com">'. T_("Forgot your pin?") . '</a>');
						$step = 'pin';
					}
					elseif($count_log > 3)
					{
						debug::title(T_("In case of entering an invalid pin, you will be blocked"));
						$step = 'pin';
					}
					else
					{
						$this->log_sleep_code('invalid:pin');
						$step = 'pin';
					}
				}
				break;
			// system in step 2
			// we check the pin code
			case 'code':
				if($this->verify_check())
				{
					// the verification code is true
					// set login
					debug::title(T_("Logged in successfully"));
					$this->login_set();
					$step = 'login';
				}
				else
				{
					$this->log('user:verfication:invalid:code');
					$wait = $count_log = $this->log_sleep_code('invalid:code');
					if($count_log >= 5)
					{
						debug::title(T_("You are trying to cheat us!"));
						$step = 'block';

					}
					elseif($count_log > 3)
					{
						debug::title(T_("Too many wrong inputs! Be Careful."));
						$step = 'code';
					}
					else
					{
						debug::title(T_("Invalid verfication code"));
						$step = 'code';
					}
				}
				break;

				// the user send password
				// we are blocked this user one more time
			case 'password':
				$step = 'mobile';
				$wait = 10;
				break;

				// the user was blocked
			case 'block':
				debug::title(T_("You are blocked"));
				$step = 'mobile';
				break;

			default:
				debug::title(T_("Invalid step"));
				$this->log('user:verfication:invalid:step');
				$this->counter('user:verfication:invalid:step', true);
				$step = 'mobile';
				$wait = 15;
				break;
		}
		// in dev mode we wait for 0 second
		if(Tld === 'dev')
		{
			$wait = 0;
		}

		debug::msg('step', $step);
		debug::msg('wait', $wait);
	}
}
?>