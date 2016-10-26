<?php
namespace lib\db;

class tg_session
{
	public static $start = false;
	public static $data, $user_id;

	public static function start($_user_id = null)
	{
		$user_id = $_user_id;
		if(!$user_id)
		{
			$user_id = self::$user_id;
		}
		$get_sesstion = "SELECT * FROM options
		WHERE options.option_cat = 'telegram' AND
		options.user_id = $user_id AND
		options.option_key = 'session' AND
		options.option_value = '$user_id'
		LIMIT 1";
		$result = \lib\db::get($get_sesstion);
		if($result)
		{
			self::$start = true;
		}
		$result = \lib\utility\filter::meta_decode($result, null, ['return_object' => true]);
		if(array_key_exists(0, $result))
		{
			$result = $result[0];
		}
		if(array_key_exists('option_meta', $result))
		{
			self::$data = $result;
		}
		else
		{
			self::$data = (object) array();
		}
		return self::$data;
	}

	public static function set($_keys, $_value)
	{
		if(!self::$start)
		{
			self::start();
		}
		$args = func_get_args();
		$arg_value = end($args);
		$keys = array_splice($args, 0 , count($args) -1);
		$object = self::$data;
		if(count($keys) === 1)
		{
			return $object = $_value;
		}
		elseif (count($keys) === 2) {
			return $object->$_keys = $_value;
		}
		$prefix_keys = array_splice($keys, 0, count($keys) -1);
		$arg_key = end($keys);
		foreach ($prefix_keys as $key => $value) {
			if(!isset($object->$value))
			{
				$object->$value = (object) array();
			}
			$object = $object->$value;
		}
		return $object->$arg_key = $arg_value;
	}

	public static function get(...$_keys)
	{
		$object = self::$data;
		foreach ($_keys as $key => $value) {
			if(!isset($object->$value))
			{
				return null;
			}
			$object = $object->$value;
		}
		return $object;
	}

	public static function push($_keys, $_value)
	{
		$args = func_get_args();
		$arg_value = end($args);
		$keys = array_splice($args, 0 , count($args) -1);
		$is_exists = self::get(...$keys);
		$x = $keys;
		if(!$is_exists)
		{
			array_push($keys, [$_value]);
			self::set(...$keys);
		}
		else
		{
			array_push($is_exists, $_value);
			array_push($keys, $is_exists);
			self::set(...$keys);
		}
	}

	public static function save($_user_id = null)
	{
		$user_id = $_user_id;
		if(!$user_id)
		{
			$user_id = self::$user_id;
		}
		$meta = json_encode(self::$data);
		$query = "INSERT INTO options SET
		options.user_id = $user_id,
		options.option_cat = 'telegram',
		options.option_key = 'session',
		options.option_value = '$user_id',
		options.option_meta = '$meta'
		ON DUPLICATE KEY UPDATE
		options.option_meta = '$meta'
		";
		return \lib\db::query($query);
	}
}
?>