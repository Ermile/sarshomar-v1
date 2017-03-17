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
			$this->counter('enter:send:max:input');
			$this->log('enter:send:password:notempty');
			return false;
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
	 * check valid mobile
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function check_valid_mobile_username()
	{
		$return = 'code';
		if(!empty($this->user_data))
		{
			if(
				array_key_exists('user_username', $this->user_data) &&
				array_key_exists('user_pass', $this->user_data) &&
				array_key_exists('user_status', $this->user_data)
			  )
			{
				if(
					$this->user_data['user_username'] &&
					$this->user_data['user_status'] === 'active' &&
					!is_null($this->user_data['user_pass'])
				  )
				{
					if(is_numeric($this->mobile) && intval($this->mobile) > 9999999 && intval($this->mobile) < 99999999999)
					{
						$this->mobile = utility\filter::mobile($this->mobile);
						return 'code';
					}

					$return = 'pin';
				}
			}

			if(array_key_exists('user_status', $this->user_data))
			{
				switch ($this->user_data['user_status'])
				{
					case 'active':
						$return = 'code';
						break;

					case 'block':
						\lib\debug::title(T_("Please enter a valid number"));
						// save log to use block number
						$this->log('enter:use:blocked:mobile');
						$this->counter('enter:use:blocked:mobile');
						$return = 'invalid';
						break;

					default:
						$return = 'code';
						break;
				}
			}
		}
		return $return;
	}
}
?>