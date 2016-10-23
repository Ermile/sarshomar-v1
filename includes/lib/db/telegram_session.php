<?php
namespace lib\db;

class telegram_session
{
	public static $start = false;
	public static $data = [];

	public static function start($_user_id = null)
	{
		$get_sesstion = "SELECT * FROM options
		WHERE options.option_cat = 'telegram_{$_user_id}'AND
		options.option_key = 'session'
		LIMIT 1";
		$result = \lib\utility\filter::meta_decode(\lib\db::get($query));

		self::$data = $result;
		return $result;
	}

	public static function set($_keys, $_value)
	{
		$args = func_get_args();
		$value = end($args);
		$keys = array_splice($args, 0 , count($args) -1);
		foreach ($keys as $key => $value) {

		}
	}
}
?>