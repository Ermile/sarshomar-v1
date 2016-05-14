<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class steps
{
	public static function start($_name)
	{
		$_SESSION['tg']['action']  = $_name;
		$_SESSION['tg']['step']    = 1;
	}


	public static function stop()
	{
		unset($_SESSION['tg']['action']);
		unset($_SESSION['tg']['step']);
		unset($_SESSION['tg']['counter']);
	}

	public static function counterPlus($_num = 1)
	{
		if(isset($_SESSION['tg']['counter']))
		{
			$_SESSION['tg']['counter'] += $_num;
		}
		else
		{
			$_SESSION['tg']['counter'] = $_num;
		}
	}

	public static function counter($_increase = true)
	{
		if($_increase)
		{
			self::counterPlus();
		}
		if(isset($_SESSION['tg']['counter']))
		{
			return $_SESSION['tg']['counter'];
		}
		return null;
	}


	public static function next($_num = 1)
	{
		// if want to go to next steps dont pass parameter
		return self::goto($_SESSION['tg']['step'] + $_num);
	}


	public static function goto($_step)
	{
		if(!is_int($_step))
		{
			return false;
		}

		$_SESSION['tg']['step'] = $_step;
	}


	public static function check($_text)
	{
		// $spost    = isset($_POST['PHPSESSID'])? $_POST['PHPSESSID']: 'hich!';
		// $tmp_text =
		// "user_id_: ".   bot::$user_id.
		// "\n id: ".      session_id().
		// "\n id-post: ". $spost.

		// "\n name: ".    session_name().
		// "\n session: ". json_encode($_SESSION);

		// // for debug
		// $tmp =
		// [
		// 	'text' => $tmp_text
		// ];
		// // $a = bot::sendResponse($tmp);


		if(isset($_SESSION['tg']['action']))
		{
			$currentStep = 'step'. $_SESSION['tg']['step'];
			if($_text === '/done' || $_text === '/end'  || $_text === '/stop')
			{
				$currentStep = 'stop';
			}
			$call        = bot::$cmdFolder. 'steps_'. $_SESSION['tg']['action'];
			$funcName    = $call. '::'. $currentStep;

			// generate func name
			if(is_callable($funcName))
			{
				// get and return response
				return call_user_func($funcName, $_text);
			}
		}
	}
}
?>