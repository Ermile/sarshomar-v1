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
	public function is_bot()
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
		if(count($input) > 2)
		{
			$this->log('enter:send:max:input', count($input));
			$this->counter('enter:send:max:input');
			return false;
		}

		if(isset($input['username']))
		{
			if(isset($input['password']))
			{
				$this->counter('enter:send:max:input');
				$this->log('enter:send:username:password');
				return false;
			}
		}

		if(isset($input['mobile']) && count($input) === 1)
		{
			return 'mobile';
		}

		if(isset($input['mobile']) && isset($input['code']) && count($input) === 2)
		{
			return 'code';
		}

		if(isset($input['mobile']) && isset($input['pin']) && count($input) === 2)
		{
			return 'pin';
		}

		return false;
	}


	/**
	 * check valid mobile
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function valid_mobile()
	{
		$exist = \lib\db\users::get_by_mobile($this->mobile);
		if(!empty($exist))
		{
			$this->exist = true;
			$this->user_data = $exist;
			if(isset($exist['user_status']))
			{
				switch ($exist['user_status'])
				{
					case 'active':
						return true;
						break;

					case 'block':
						\lib\debug::title(T_("Please enter a valid number"));
						// save log to use block number
						$this->log('enter:use:blocked:mobile');
						$this->counter('enter:use:blocked:mobile');
						return false;
						break;

					default:
						return true;
						break;
				}
			}
			return true;
		}
		return true;
	}
}
?>