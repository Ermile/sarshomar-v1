<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait verify
{

	public function call_mobile()
	{
		// check is valid mobile by kavenegar
		// if valid call and return true
		// else return false

		// $this->kavenegar();
		// exit();

	}


	/**
	 * { function_description }
	 */
	public function kavenegar()
	{
		$myperm     = $this->option('account');
		if(!$myperm)
		{
			$myperm = 'NULL';
		}
		$user_id     = \lib\db\users::signup(['mobile' => $mymobile, 'password' =>  $mypass, 'permission' =>  $myperm, 'port' => 'site']);
		if($user_id)
		{
			// generate verification code
			// save in logs table
			// set SESSION verification_mobile
			$code = \lib\utility\filter::generate_verification_code($user_id, $mymobile);
			if($code)
			{
				$service_name = \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
				$request = [
					'mobile' 		=> $mymobile,
					'template' 		=> $service_name . '-' . \lib\define::get_language(),
					'token'			=> $code,
					'type'			=> 'call'
					];
					$users_count = \lib\db\users::get_count();
					if(is_int($users_count) && $users_count > 1000)
					{
						$request['template'] =  $service_name . '-' . $this->module() . '-' . \lib\define::get_language();
						$request['token2'] 	= $users_count;
					}
				\lib\utility\sms::send($request, 'verify');
				debug::true(T_("Register successfully"));
				$_SESSION['tmp']['verify_mobile'] = $mymobile;
				$_SESSION['tmp']['verify_mobile_time'] = time() + (5*60);
				$this->redirector()->set_url('verification');
			}
			else
			{
				debug::error(T_("Please contact to administrator!"));
			}
		}
		elseif($user_id === false)
		{
			debug::error(T_("Mobile number exist!"));
		}
		else
		{
			debug::error(T_("Please contact to administrator!"));
		}
	}
}
?>