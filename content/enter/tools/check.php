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
				case '1':
					return 'step1';
					break;

				case '2':
					return 'step2';
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
	public function check_valid_mobile()
	{
		if(!empty($this->user_data))
		{
			if(isset($this->user_data['user_status']))
			{
				switch ($this->user_data['user_status'])
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