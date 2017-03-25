<?php
namespace lib\utility;

/**
 * Class for synchronize.
 */
class sync
{

	private static $new_user_id;
	private static $old_user_id;
	private static $mobile;
	// check error was happend
	private static $has_error = false;

	use sync\web_guest;
	use sync\telegram;
	use sync\data;

	/**
	 * return status by db_return class
	 *
	 * @param      <type>  $_status  The status
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function status($_status)
	{
		$return = new \lib\db\db_return();
		return $return->set_ok($_status);
	}
}
?>