<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\db;
use \lib\utility\shortURL;

trait check
{

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
	 * check permission from self::$permission
	 *
	 * @param      <type>   $_permission  The permission
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function poll_check_permission($_content = null, $_permission = null, $_accions = null)
	{
		$permission = new \lib\utility\permission;
		return $permission->access($_content, $_permission, $_accions);
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
			return debug::error(T_("Invalid parameter file"), 'file', 'arguments');
		}

		$attachment_id = shortURL::decode($_code);
		$attachment = self::get_poll($attachment_id);
		if(!$attachment)
		{
			return debug::error(T_("Attachment not found"), 'file', 'arguments');
		}

		if(!isset($attachment['type']) || (isset($attachment['type']) && $attachment['type'] != 'attachment'))
		{
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