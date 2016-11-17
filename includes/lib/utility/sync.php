<?php
namespace lib\utility;
/**
 * Class for synchronize.
 */
class sync
{
	/**
	 * get the mobile of web service and the telegram id
	 * and sync
	 *
	 * @param      <type>  $_web_mobile   The web mobile
	 * @param      <type>  $_telegram_id  The telegram identifier
	 */
	public static function web_telegram($_web_mobile, $_telegram_id)
	{
		$web = \lib\db\users::get_by_mobile(\lib\utility\filter::mobile($_web_mobile));
		if(!$web || !isset($web['id']))
		{
			return false;
		}

		$web_id = $web['id'];


	}
}
?>