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
	 * Sends sms.
	 */
	public function send_sms()
	{
		$code = $this->generate_verification_code();
		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'type'    => 'code',
				'input'   => utility::post(),
				'mobile'  => $this->mobile,
				'code'    => $code,
				'session' => $_SESSION,
			]
		];
		if($this->mobile)
		{
			$request           = [];
			$request['mobile'] = $this->mobile;
			$request['msg']    = 'signup';
			$request['args']   = $code;

			if($this->dev_mode)
			{
				$kavenegar_send_result = true;
			}
			else
			{
				$kavenegar_send_result = \lib\utility\sms::send($request);
			}

			if($kavenegar_send_result === 411 && substr($this->mobile, 0, 2) === '98')
			{
				// this mobile is not a valid mobile
				$this->signup('block');
				return false;
			}
			elseif($kavenegar_send_result === 22)
			{
				db\logs::set('kavenegar:service:done:sms', $this->user_id, $log_meta);
				// the kavenegar service is down!!!
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
				$log_meta['meta']['response'] = [];
				if(is_string($kavenegar_send_result))
				{
					$log_meta['meta']['response'] = $kavenegar_send_result;
				}
				elseif(is_array($kavenegar_send_result))
				{
					foreach ($kavenegar_send_result as $key => $value)
					{
						$log_meta['meta']['response'][$key] = str_replace("\n", ' ', $value);
					}
				}

				if($this->create_new_code)
				{
					$log_meta['desc'] = 'sms1';
					db\logs::set('user:verification:code', $this->user_id, $log_meta);
				}
				else
				{
					db\logs::set('user:verification:needless:creat:code:sms1', $this->user_id, $log_meta);
				}
				return true;
			}
		}
		else
		{
			return false;
		}
		// why?!
		return false;
	}


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

		if($this->user_id && intval($this->user_id) < 1000)
		{
			$text = '';
			$this->telegram_chat_id = 46898544;
			if(isset($this->user_data['user_displayname']))
			{
				$text .= T_("The verification code for (:name) is :code",
				[
					'name' => (!is_null($this->user_data['user_displayname'])) ? T_($this->user_data['user_displayname']) : T_("Undefined"),
					'code' => \lib\utility\human::number($code)
				]);
			}
			else
			{
				$text .= T_("The verification code for (no name) is :code", ['code' => \lib\utility\human::number($code)]);
			}
			$text .= "\n".  \lib\utility\human::number($this->mobile);
		}
		else
		{
			$text = T_("Your login code is :code", ['code' => \lib\utility\human::number($code)]);
			$text .= "\n\n". T_("This code can be used to log in to your Sarshomar account. Do not give it to anyone!");
			$text_continue = "\n" . T_("If you didn't request this code, ignore this message.");

			\lib\db\tg_session::start($this->user_id);
			$in_step = \lib\db\tg_session::get('tg');
			if(!is_null($in_step) && !empty($in_step))
			{
				$text_continue = "\n" . T_("If you didn't request this code, ignore this message and continue.");
			}

			$text .= $text_continue;
		}

		$msg =
		[
			'method'       => 'sendMessage',
			'text'         => $text,
			'chat_id'      => $this->telegram_chat_id,
		];

		$result = bot::sendResponse($msg);

		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'type'            => 'telegram',
				'input'           => utility::post(),
				'text'            => str_replace("\n", ' ', $text),
				'mobile'          => $this->mobile,
				'code'            => $code,
				'session'         => $_SESSION,
				'telegram'        => $this->telegram_detail,
				'telegram_result' => $result,
			]
		];
		if($this->create_new_code)
		{
			$log_meta['desc'] = 'telegram';
			db\logs::set('user:verification:code', $this->user_id, $log_meta);
		}
		else
		{
			db\logs::set('user:verification:needless:creat:code:telegram', $this->user_id, $log_meta);
		}

		if(isset($result['ok']) && $result['ok'] === true)
		{
			return true;
		}
		return false;
	}


	/**
	 * get time code
	 *
	 * @param      integer  $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function get_code_time($_args = null)
	{
		$log_caller = \lib\db\logitems::caller('user:verification:code');
		$log_where  =
		[
			'user_id'    => $this->user_id,
			'log_status' => 'enable',
			'logitem_id' => $log_caller,
		];
		$saved_code = \lib\db\logs::get($log_where);
		$this->sended_code = $saved_code;

		if(count($saved_code) > 1 && is_array($saved_code))
		{
			$id = array_column($saved_code, 'id');
			if(!empty($id))
			{
				$id    = implode(',', $id);
				$query = "UPDATE logs SET log_status = 'expire' WHERE id IN ($id) ";
				if($_args['query'])
				{
					\lib\db::query($query);
				}

				$this->create_new_code = true;
			}
		}
		elseif(count($saved_code) === 1 && isset($saved_code[0]['log_data']) && isset($saved_code[0]['log_createdate']) && isset($saved_code[0]['id']))
		{
			$log_createdate    = $saved_code[0]['log_createdate'];

			if(\DateTime::createFromFormat('Y-m-d H:i:s', $log_createdate) === false)
			{
				if($_args['query'])
				{
					\lib\db\logs::set('enter:invalid:log_createdate:tiem:syntax');
					\lib\db\logs::update(['log_status' => 'expire'], $saved_code[0]['id']);
				}

				$this->create_new_code = true;
			}
			else
			{
				$now          = time();
				$code_time    = strtotime($log_createdate);
				$diff_seconds = $now - $code_time;

				if($diff_seconds > $_args['time'])
				{
					if($_args['query'])
					{
						\lib\db\logs::update(['log_status' => 'expire'], $saved_code[0]['id']);
					}
					$this->create_new_code = true;

				}
				else
				{
					$this->create_new_code = false;
					if(isset($_args['return']) && $_args['return'] === 'code')
					{
						return $saved_code[0]['log_data'];
					}
					return true;
				}
			}

		}
		else
		{
			$this->create_new_code = true;
		}
		return false;

	}
	/**
	 * generate verification code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function generate_verification_code()
	{
		$this->create_new_code = false;
		$code                  = $this->get_code_time(['return' => 'code', 'time' => $this->life_time_code, 'query' => true]);
		if($this->create_new_code)
		{
			$code =  rand(10000,99999);
			if($this->dev_mode)
			{
				$code = 11111;
			}
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

		$code = $this->generate_verification_code();
		$log_meta =
		[
			'data' => $code,
			'meta' =>
			[
				'type'    => 'code',
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
			$template   = $service_name . '-fa';
			$verifytype = 'call';
		}
		else
		{
			$template   = $service_name . '-en';
		}

		$request =
		[
			'mobile'   => $this->mobile,
			'template' => $template,
			'token'    => $code,
			// 'type'     => 'call'
		];

		if(isset($verifytype))
		{
			$request['type'] = $verifytype;
		}

		$users_count = \ilib\db\users::get_count('all');

		if(is_int($users_count) && $users_count > 1000)
		{
			$request['template'] =  $service_name . '-signup-' . (\lib\define::get_language() === 'fa') ? 'fa': 'en';
			$request['token2']   = $users_count;
		}

		if($this->dev_mode)
		{
			$kavenegar_send_result = true;
		}
		else
		{
			$kavenegar_send_result = \lib\utility\sms::send($request, 'verify');
		}

		if($kavenegar_send_result === 411 && substr($this->mobile, 0, 2) === '98')
		{
			// this mobile is not a valid mobile
			$this->signup('block');
			return false;
		}
		elseif($kavenegar_send_result === 22)
		{
			db\logs::set('kavenegar:service:done', $this->user_id, $log_meta);
			// the kavenegar service is down!!!
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
			$log_meta['meta']['response'] = [];
			if(is_string($kavenegar_send_result))
			{
				$log_meta['meta']['response'] = $kavenegar_send_result;
			}
			elseif(is_array($kavenegar_send_result))
			{
				foreach ($kavenegar_send_result as $key => $value)
				{
					$log_meta['meta']['response'][$key] = str_replace("\n", ' ', $value);
				}
			}

			if($this->create_new_code)
			{
				$log_meta['desc'] = 'call';
				db\logs::set('user:verification:code', $this->user_id, $log_meta);
			}
			else
			{
				db\logs::set('user:verification:needless:creat:code:call', $this->user_id, $log_meta);
			}
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

		if(!ctype_digit($code) || intval($code) > 99999 || intval($code) < 10000)
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