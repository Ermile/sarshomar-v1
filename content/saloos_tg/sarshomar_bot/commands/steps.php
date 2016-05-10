<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class steps
{
	public static function start($_name)
	{
		$user_id = bot::response('from');
		$_SESSION['tg'][$user_id]['action'] = $_name;
		$_SESSION['tg'][$user_id]['step']   = 1;

		return $user_id;
	}


	public static function stop()
	{
		$user_id = bot::response('from');
		unset($_SESSION['tg'][$user_id]['action']);
		unset($_SESSION['tg'][$user_id]['step']);

		return $user_id;
	}


	public static function increase()
	{
		$user_id = bot::response('from');
		// if want to increase steps dont pass parameter
		$_step = $_SESSION['tg'][$user_id]['step']+1;
		return self::goto($_step);
	}


	public static function goto($_step)
	{
		if(!is_int($_step))
		{
			return false;
		}

		$user_id = bot::response('from');
		$_SESSION['tg'][$user_id]['step'] = $_step;

		return $user_id;
	}


	public static function check($_text)
	{
		$user_id = bot::response('from');
		if(isset($_SESSION['tg'][$user_id]['action']))
		{
			$call        = 'steps_'. $_SESSION['tg'][$user_id]['action'];
			$currentStep = 'step'. $_SESSION['tg'][$user_id]['step'];
			$funcName    = $call. '::'. $currentStep;

			// generate func name
			if(is_callable($funcName))
			{
				var_dump('callable');
				// get response
				$answer = call_user_func($funcName, $_text, $user_id);
				return $answer;
			}
		}
	}
}
?>