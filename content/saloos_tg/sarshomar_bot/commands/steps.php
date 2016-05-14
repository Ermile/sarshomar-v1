<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class steps
{


	/**
	 * define variables
	 * @param  [type] $_name name of current step for call specefic file
	 * @return [type]        [description]
	 */
	public static function start($_name)
	{
		// name of steps for call specefic file
		self::set('name', $_name);
		// counter of steps number, increase automatically
		self::set('counter', 1);
		// pointer of current step, can change by user commands
		self::set('pointer', 1);
		// extra counter for some other use,
		self::set('num', 0);
		// save text of each steps
		self::set('text', []);
		// save last entered text
		self::set('last', null);
	}


	/**
	 * delete session steps value
	 * @return [type] [description]
	 */
	public static function stop()
	{
		unset($_SESSION['tg']['steps']);
	}


	/**
	 * set specefic key of steps
	 * @param  string $_key   name of key
	 * @param  string $_value value of this key
	 * @return [type]         [description]
	 */
	public static function set($_key, $_value)
	{
		// some condition for specefic keys
		switch ($_key)
		{
			case 'text':
				$_SESSION['tg']['steps'][$_key][]   = $_value;
				$_SESSION['tg']['steps']['last']    = $_value;
				// $_SESSION['tg']['steps']['counter'] += $_value;
				self::plus('counter');
				break;

			case 'pointer':
				self::plus('counter');
			default:
				$_SESSION['tg']['steps'][$_key] = $_value;
				// return that value was set!
				break;
		}
		// return true because it's okay!
		return true;
	}


	/**
	 * get specefic key of steps
	 * @param  string $_key [description]
	 * @return [type]       [description]
	 */
	public static function get($_key = null)
	{
		if($_key === null)
		{
			if(isset($_SESSION['tg']['steps']))
			{
				return $_SESSION['tg']['steps'];
			}
		}
		elseif($_key === false)
		{
			if(isset($_SESSION['tg']['steps']))
			{
				return true;
			}
		}
		elseif(isset($_SESSION['tg']['steps'][$_key]))
		{
			return $_SESSION['tg']['steps'][$_key];
		}
		elseif(isset($_SESSION['tg']['steps']))
		{
			return null;
		}

		return false;
	}


	/**
	 * go to next step
	 * @param  integer  $_num number of jumping
	 * @return function       result of jump
	 */
	public static function plus($_key = 'pointer', $_num = 1, $_relative = true)
	{
		if($_relative)
		{
			$_num = self::get($_key) + $_num;
		}

		return self::set($_key, $_num);
	}


	/**
	 * [check description]
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	public static function check($_text)
	{
		// $tmp_text =
		// "user_id_: ".   bot::$user_id.
		// "\n id: ".      session_id().

		// "\n name: ".    session_name().
		// "\n session: ". json_encode($_SESSION);

		// // for debug
		// $tmp =
		// [
		// 	'text' => $tmp_text
		// ];
		// $a = bot::sendResponse($tmp);


		// if before this message steps started
		if(self::get(false))
		{
			// save text
			self::set('text', $_text);
			// calc current step
			switch ($_text)
			{
				case '/done':
				case '/end':
				case '/stop':
				case '/cancel':
					// if user want to stop current steps
					$currentStep = 'stop';
					break;

				default:
					$currentStep = 'step'. self::get('pointer');
					break;
			}
			// create namespace and class name
			$call        = bot::$cmdFolder. 'steps_'. self::get('name');
			// create function full name
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