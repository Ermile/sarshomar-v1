<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\telegram\tg as bot;

trait verify
{



	/**
	 * send verification code to the user telegram
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function verify_send_telegram()
	{

		$code = $this->generate_verification_code();
		$text = T_("Your login code is :code", ['code' => $code]);
		$text .= "\n\n". T_("This code can be used to log in to your Sarshomar account. We never ask it for anything else. Do not give it to anyone!");
		$text .= "\n" . T_("If you didn't request this code by trying to log in on another device, simply ignore this message.");

		\lib\db\tg_session::start($this->user_id);
		$in_step = \lib\db\tg_session::get('tg');
		if(!is_null($in_step) && !empty($in_step))
		{
			$text .= T_("شما در حال انجام پروسه ای هستید. اگر شما این درخواست را نداده اید پروسه خود را ادامه دهید");
		}

		$msg =
		[
			'method'       => 'sendMessage',
			'text'         => $text,
			'chat_id'      => $this->telegram_chat_id,
		];
		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'input'        => utility::post(),
				'text'         => $text,
				'mobile'       => $this->mobile,
				'code'         => $code,
				'session'      => $_SESSION,
				'telegram'     => $this->telegram_detail,
				'telegram_msg' => $msg,
			]
		];

		db\logs::set('user:verification:code', $this->user_id, $log_meta);
		$result = bot::sendResponse($msg);

		if(isset($result['ok']) && $result['ok'] === true)
		{
			return true;
		}
		return false;

	}


	public function expire_old_code()
	{

	}


	/**
	 * generate verification code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function generate_verification_code()
	{
		$code =  rand(100000,999999);
		if(Tld === 'dev')
		{
			$code = 111111;
		}
		return $code;
	}


	/**
	 * send verification by call
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function verify_call_mobile()
	{
		$this->expire_old_code();

		$code = $this->generate_verification_code();
		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'input'   => utility::post(),
				'mobile'  => $this->mobile,
				'code'    => $code,
				'session' => $_SESSION,
			]
		];


		$service_name = 'sarshomar';
		$language     = \lib\define::get_language();

		if($language === 'fa')
		{
			$template = $service_name . '-fa';
		}
		else
		{
			$template = $service_name . '-en';
		}

		$request =
		[
			'mobile'   => $this->mobile,
			'template' => $template,
			'token'    => $code,
			'type'     => 'call'
		];

		$users_count = \ilib\db\users::get_count('all');

		if(is_int($users_count) && $users_count > 1000)
		{
			$request['template'] =  $service_name . '-signup-' . (\lib\define::get_language() === 'fa') ? 'fa': 'en';
			$request['token2']   = $users_count;
		}
		if(Tld === 'dev')
		{
			$check_valid_mobile = true;
		}
		else
		{
			$check_valid_mobile = \lib\utility\sms::send($request, 'verify');
		}

		if($check_valid_mobile === 411)
		{
			// this mobile is not a valid mobile
			$this->signup('block');
			return false;
		}
		else
		{
			if(!$this->user_id)
			{
				if($this->signup)
				{
					// singn up by this mobile
					$this->user_id = $this->signup();
				}
				else
				{
					db\logs::set('user:signup:lock:try:signup', $this->user_id, $log_meta);
				}
			}
			$log_meta['meta']['response'] = $check_valid_mobile;
			db\logs::set('user:verification:code', $this->user_id, $log_meta);
			return true;
		}
		// why?!
		return false;
	}


	/**
	 * check verification code
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function verify_check()
	{

		$code = utility::post('code');
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'mobile'  => $this->mobile,
				'code'    => $code,
				'session' => $_SESSION,
			]
		];

		if(!ctype_digit($code) || intval($code) > 999999 || intval($code) < 100000)
		{
			db\logs::set('user:verification:invalid:code', $this->user_id, $log_meta);
			$this->counter('user:verification:invalid:code');
			return false;
		}

		$where =
		[
			'user_id'    => $this->user_id,
			'log_data'   => $code,
			'log_status' => 'enable',
			'limit'      => 1,
		];
		$result = \lib\db\logs::get($where);

		if(empty($result) || !isset($result['log_data']) || !isset($result['user_id']) || !isset($result['id']))
		{
			$this->counter('user:verification:invalid:code');
			return false;
		}

		if(intval($result['log_data']) === intval($code))
		{
			db\logs::set('user:verification:success', $this->user_id, $log_meta);
			\lib\db\logs::update(['log_status' => 'expire'], $result['id']);
			return true;
		}
		else
		{
			db\logs::set('user:verification:another:code', $this->user_id, $log_meta);
			$this->counter('user:verification:invalid:code');
			return false;
		}
	}


	/**
	 * check pin code
	 */
	public function verify_pin_check()
	{
		$pin      = utility::post('pin');
		$password = null;

		if(array_key_exists('user_pass', $this->user_data))
		{
			$password = $this->user_data['user_pass'];
		}

		if(utility::hasher($pin, $password))
		{
			return true;
		}
		return false;
	}
}
?>