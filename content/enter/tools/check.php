<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait check
{

	/**
	 * Determines if bottom.
	 *
	 * @return     boolean  True if bottom, False otherwise.
	 */
	public function check_is_bot()
	{
		$is_bot = utility\visitor::isBot();
		if($is_bot === 'NULL')
		{
			return false;
		}
		return true;
	}


	/**
	 * check inputs
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function check_input()
	{
		$input = utility::post();

		if(count($input) > 5)
		{
			$this->log('enter:send:max:input', count($input));
			$this->counter('enter:send:max:input');
			return false;
		}


		if(isset($input['password']) && $input['password'])
		{
			$this->log('enter:send:password:notempty');
			if($this->counter('enter:send:max:input') >= 3)
			{
				$this->counter('enter:send:max:input', true);
				return 'block';
			}
			debug::title(T_("Don't!"));
			return 'password';
		}

		if(isset($input['step']))
		{
			switch ($input['step'])
			{
				case 'mobile':
				case 'pin':
				case 'code':
					// get code
					return $input['step'];
					break;

				case 'resend':
					if($this->check_access_resend())
					{
						return 'resend';
					}
					else
					{
						return 'fake_resend';
					}

					break;
				default:
					return false;
					break;
			}
		}
		else
		{
			return false;
		}
		return false;
	}



	/**
	 * access resend code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function check_access_resend()
	{
		$this->get_code_time(['time' => $this->resend_after, 'query' => false]);
		// return
		return true;
	}


	/**
	 * check valid mobile
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function find_send_way($_current = null)
	{
		// sarshomar admin send code from admin
		if($this->user_id && intval($this->user_id) < 1000)
		{
			return 'telegram';
		}

		$return = 'sms1';
		if(!empty($this->user_data))
		{
			if(
				array_key_exists('user_username', $this->user_data) &&
				array_key_exists('user_pass', $this->user_data) &&
				array_key_exists('user_status', $this->user_data)
			  )
			{
				if(
					$this->user_data['user_status'] === 'active' &&
					$this->user_data['user_pass']
				  )
				{
					if($_current === 'pin')
					{
						// current way is pin
						// need to other way
					}
					else
					{
						return 'pin';
					}
				}
			}

			// check if this user have a telegram id and start the telegram
			$telegram_id =
			[
				'user_id'    => $this->user_id,
				'option_cat' => 'telegram',
				'option_key' => 'id',
				'limit'      => 1,
			];
			$telegram_id = db\options::get($telegram_id);
			$this->telegram_detail['telegram_id'] =  $telegram_id;
			if(!empty($telegram_id) && isset($telegram_id['value']) && $telegram_id['value'])
			{
				$telegram_start_status =
				[
					'user_id'      => $this->user_id,
					'option_cat'   => 'user_detail_'. $this->user_id,
					'option_key'   => 'telegram_start_status',
					'option_value' => 'start',
					'limit'        => 1,
				];
				$telegram_start_status = db\options::get($telegram_start_status);
				if(!empty($telegram_start_status))
				{
					$this->telegram_detail['telegram_start_status'] =  $telegram_start_status;
					$this->telegram_chat_id = $telegram_id['value'];
					if($_current === 'telegram')
					{
						// current way is telegram
						// need to other way
					}
					else
					{
						return 'telegram';
					}
				}
			}

			if(array_key_exists('user_status', $this->user_data))
			{
				switch ($this->user_data['user_status'])
				{
					case 'active':
						// $return = 'code';
						$return = 'sms1';
						break;

					case 'block':
						\lib\debug::title(T_("Please enter a valid number"));
						// save log to use block number
						$this->log('enter:use:blocked:mobile');
						$this->counter('enter:use:blocked:mobile');
						$return = 'invalid';
						break;

					default:
						$return = 'sms1';
						break;
				}
			}
		}
		return $return;
	}
}
?>