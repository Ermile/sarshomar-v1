<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\db;
use \lib\utility\shortURL;

trait check
{
	/**
	 * check permission from self::$permission
	 *
	 * @param      <type>   $_permission  The permission
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function permission($_content = null, $_permission = null, $_accions = null)
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