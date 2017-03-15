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
	public $exist     = false;
	public $user_data = [];


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
		if($this->is_bot())
		{
			\lib\error::access(T_("You are bot"));
		}
		$this->post_enter($_args);
	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_enter($_args)
	{
		$ok = false;
		switch ($this->check_input())
		{
			case 'mobile':
				$mobile = '09109610612';
				$this->mobile = utility\filter::mobile($mobile);
				$valid = $this->valid_mobile();
				if($valid)
				{
					// call new mobile
					if($this->call_mobile())
					{
						$ok = true;
					}
				}
				break;

			case 'code':


				break;

			case 'pin':

				break;

			default:
				// no thing!
				break;
		}

		debug::msg('ok', $ok);
	}
}
?>