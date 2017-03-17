<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait verify
{

	public function expire_old_code()
	{

	}

	public function verify_call_mobile()
	{
		$this->expire_old_code();

		$code = rand(10000,99999);
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

		if($code)
		{
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

			$check_valid_mobile = \lib\utility\sms::send($request, 'verify');

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
					// singn up by this mobile
					$this->user_id = $this->signup();
				}
				$log_meta['meta']['response'] = $check_valid_mobile;
				db\logs::set('user:verification:code', $this->user_id, $log_meta);
				return true;
			}
		}
		else
		{
			// code not set !!
			debug::error(T_("Please contact to administrator!"));
			return false;
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

		if(!is_numeric($code) || intval($code) > 99999 || intval($code) < 10000)
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