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

		// check the user is block
		if($this->enter_is_blocked())
		{
			$this->log('use:enter:blocked:agent:ip');
			$this->log_sleep_code('use:enter:blocked:agent:ip');
			debug::msg('step', 'block');
			debug::error(T_("You are blocked"));
			return false;
		}

		$ok          = 'false';
		// check input and get the step
		$check_input = $this->check_input();

		// load data of users by search mobile
		if($check_input)
		{
			$mobile          = utility::post('mobile');
			$this->mobile    = utility\filter::mobile($mobile);
			$this->user_data = \lib\db\users::get_by_mobile($this->mobile);
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
				$valid = $this->check_valid_mobile();
				if($valid)
				{
					// call mobile
					if($this->verify_call_mobile())
					{
						// call was send
						$ok = 'true';
					}
					else
					{
						$this->log('user:verification:invalid:mobile');
						$this->log_sleep_code('invalid:mobile');
						// this mobile is not a valid mobile
						// check by kavenegar
						$ok = 'invalid';
					}
				}
				break;
			// system in step 2
			// we check the verification code
			case 'step2':
				if($this->verify_check())
				{
					// the verification code is true
					// set login
					$this->login_set();
					$ok = 'true';
				}
				else
				{
					$this->log('user:verfication:invalid:code');
					$this->log_sleep_code('invalid:code');
					$ok = 'false';
				}
				break;

			default:
				$this->log('user:verfication:invalid:input');
				$this->counter('user:verfication:invalid:input');
				// invalid input
				$ok = 'false';
				break;
		}

		debug::msg('step', $ok);
	}
}
?>