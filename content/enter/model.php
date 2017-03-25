<?php
namespace content\enter;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \mvc\model
{

	public $mobile            = null;
	public $username          = null;
	public $pin               = null;
	public $code              = null;
	public $user_data         = [];
	public $user_id           = null;
	public $guest_user_id     = null;
	public $signup            = false;
	public $telegram_chat_id  = null;
	public $telegram_detail   = [];
	// public $block_type     = 'ip-agent';
	public $block_type        = 'session';
	public $is_guest          = false;

	// config to send to javaScript
	public $step              = 'mobile';
	public $send              = 'code';
	public $wait              = 0;
	// show resende link ofter
	public $resend_after      = ((60 * 2) + 30); // 2.5 min
	// life time code to expire
	public $life_time_code    = 60 * 5; // 5 min

	public $sended_code       = [];
	public $create_new_code   = false;
	public $resend_rate =
	[
		'telegram',
		'code',
		'main_sms',
		'secondary_sms',
	];

	use tools\check;
	use tools\log;
	use tools\login;
	use tools\signup;
	use tools\verify;

	use tools\step\mobile;
	use tools\step\code;
	use tools\step\pin;
	use tools\step\resend;
	use tools\step\call_mobile;


	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_enter($_args)
	{
		if($this->login() && \lib\utility\users::is_guest($this->login('id')))
		{
			$this->is_guest = true;
		}

		if($this->login())
		{
			if(!$this->is_guest)
			{
				$this->redirector('@')->redirect();
				return;
			}
		}

		if($this->check_is_bot())
		{
			\lib\error::access(T_("You are bot"));
		}

		if($this->login_by_remember())
		{
			if(!$this->is_guest)
			{
				return;
			}
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

		// check input and get the step
		$get_step = $this->check_input();

		// load data of users by search mobile
		if($get_step)
		{
			$mobile = utility::post('mobile');
			if(ctype_digit($mobile))
			{
				$mobile          = utility\filter::mobile($mobile);
				$this->mobile    = $mobile;
				$this->user_data = \ilib\db\users::get_by_mobile($this->mobile);
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
					$this->mobile  = $this->user_data['user_mobile'];
					$this->user_id = $this->user_data['id'];
				}
				else
				{
					$this->log('use:enter:username:notexist');
					$this->log_sleep_code('use:enter:username:notexist');
					debug::msg('wait', 10);
					debug::msg('send', $this->send);
					debug::msg('step', 'mobile');
					debug::title(T_("Invalid username"));
					return false;
				}
			}
		}

		switch ($get_step)
		{
			// input in mobile
			case 'mobile':
				$way = $this->find_send_way();
				$this->step_mobile($way);
				break;

			case 'pin':
				$way = $this->find_send_way('pin');
				$this->step_pin($way);
				break;

			case 'code':
				$this->step_code();
				break;

			case 'resend':
				$resend_on = $this->step_resend();
				switch ($resend_on)
				{
					case 'telegram':
					case 'code':
						$this->step_mobile($resend_on);
						break;

					default:
						debug::title(T_("Please contact to us to help you in enter in site"));
						break;
				}
				break;

			case 'fake_resend':
				debug::title("Why hurry!?");
				$this->log('user:verfication:fake:resend');
				$this->counter('user:verfication:fake:resend', true);
				$this->step = 'mobile';
				$this->wait = 20;
				break;

			// the user send password
			// we are blocked this user one more time
			case 'password':
				$this->step = 'mobile';
				$this->wait = 10;
				break;

				// the user was blocked
			case 'block':
				debug::title(T_("You are blocked"));
				$this->step = 'mobile';
				break;

			default:
				debug::title(T_("Invalid step"));
				$this->log('user:verfication:invalid:step');
				$this->counter('user:verfication:invalid:step', true);
				$this->step = 'mobile';
				$this->wait = 15;
				break;
		}
		// in dev mode we wait for 0 second
		if(Tld === 'dev')
		{
			$this->wait = 0;
		}

		debug::msg('step', $this->step);
		debug::msg('wait', $this->wait);
		debug::msg('send', $this->send);
		debug::msg('resend_after', $this->resend_after);
	}
}
?>