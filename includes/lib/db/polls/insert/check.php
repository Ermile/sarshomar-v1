<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\db;
use \lib\utility\shortURL;

trait check
{

	/**
	 * safe user string
	 *
	 * @param      <type>  $_string  The string
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function safe_user_string($_string)
	{
		$_string = preg_replace("/[\s\t\n]+/", ' ', $_string);
		$_string = trim($_string);
		// $_string = trim($_string, '‌'); // trim half space
		// $_string = trim($_string, '‌'); // trim half space
		return $_string;
	}


	/**
	 * check if isset parameter in self::$args
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function isset_args()
	{
		$arg = func_get_args();
		$temp_args = [];
		if(self::$args && is_array(self::$args))
		{
			if(array_key_exists(0, $arg) && array_key_exists($arg[0], self::$args))
			{
				$temp_args = self::$args[$arg[0]];
			}
			else
			{
				return false;
			}

			array_shift($arg);

			if(!empty($arg))
			{
				if(is_array($temp_args))
				{
					$prev_index     = null;
					$prev_temp_args = [];

					foreach ($arg as $key => $index)
					{
						if(!is_array($temp_args))
						{
							break;
						}
						$prev_index     = $index;
						$prev_temp_args = $temp_args;
						if(array_key_exists($index, $temp_args))
						{
							$temp_args = $temp_args[$index];
						}
					}
					$end = end($arg);
					if(array_key_exists($end, $prev_temp_args))
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return true;
			}
		}
		return false;
	}


	/**
	 * check if empty poll set error
	 */
	public static function empty_poll()
	{
		$title   = false;
		$answers = false;
		$answer2 = false;

		if(self::isset_args('title') && self::$args['title'])
		{
			$title = true;
		}

		if(self::isset_args('answers'))
		{
			for ($i = 0; $i < 5; $i++)
			{
				if(
					(self::isset_args('answers', $i, 'title') && self::$args['answers'][$i]['title']) ||
					(self::isset_args('answers', $i, 'file') && self::$args['answers'][$i]['file'])
				  )
				{
					$answers = true;
				}
			}
		}

		if(!$title && !$answers)
		{
			return true;
		}
		return false;
	}


	/**
	 * check permission from self::$permission
	 *
	 * @param      <type>   $_permission  The permission
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function poll_check_permission()
	{
		\lib\permission::$user_id = self::$user_id;
		return \lib\permission::access(...func_get_args());
	}


	/**
	 * check record is attachment record
	 *
	 * @param      <type>  $_code  The code
	 *
	 * @return     array   True if attachment, False otherwise.
	 */
	public static function is_attachment($_code)
	{

		if(!preg_match("/^[". self::$args['shortURL']. "]+$/", $_code))
		{
			// \lib\db::rollback();
			\lib\db\logs::set('user:poll:attachment:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("Invalid parameter file"), 'file', 'arguments');
		}

		$attachment_id = shortURL::decode($_code);
		$attachment = self::get_poll($attachment_id);
		if(!$attachment)
		{
			// \lib\db::rollback();
			\lib\db\logs::set('user:poll:attachment:notfound', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("Attachment not found"), 'file', 'arguments');
		}

		if(!isset($attachment['type']) || (isset($attachment['type']) && $attachment['type'] != 'attachment'))
		{
			// \lib\db::rollback();
			\lib\db\logs::set('user:poll:attachment:is_not_attachment_record', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("This is not an attachment record"), 'file', 'arguments');
		}

		if(isset($attachment['status']))
		{
			switch ($attachment['status'])
			{
				case 'draft':
				case 'awaiting':
				case 'publish':
					// no thing !
					break;

				case 'stop':
				case 'pause':
				case 'trash':
				case 'deleted':
				case 'filtered':
				case 'blocked':
				case 'spam':
				case 'violence':
				case 'pornography':
				case 'schedule':
				case 'expired':
				case 'filter':
				default:
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:attachment:permission:status', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Can not use this attachment"), 'file', 'permission');
					break;
			}
		}
		$return = [];
		if(isset($attachment['meta']['url']))
		{
			$return['url'] = $attachment['meta']['url'];
		}

		if(isset($attachment['meta']['type']))
		{
			$return['type'] = $attachment['meta']['type'];
		}
		return $return;
	}
}
?>