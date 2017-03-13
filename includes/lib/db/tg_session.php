<?php
namespace lib\db;
use content\saloos_tg\sarshomarbot\commands\handle;

class tg_session
{
	public static $start = false;
	public static $data, $user_id, $data_back, $data_json;

	public static function start($_user_id = null)
	{
		if(self::$start)
		{
			return self::$data;
		}
		$user_id = $_user_id;
		if(!$user_id)
		{
			$user_id = self::$user_id;
		}
		else
		{
			self::$user_id = $user_id;
		}
		$get_sesstion = "SELECT * FROM options
		WHERE options.option_cat = 'user_detail_{$user_id}' AND
		options.user_id = $user_id AND
		options.option_key = 'telegram_session' AND
		options.option_value = 'session'
		LIMIT 1";
		$original_result = \lib\db::query($get_sesstion, true, ['resume_on_error' => true]);
		$original_result = $original_result->fetch_assoc();
		if($original_result)
		{
			self::$start = true;
		}
		if(is_array($original_result) && array_key_exists('option_meta', $original_result))
		{
			self::$data_json = $original_result['option_meta'];
			$original_result['option_meta'] = utf8_decode($original_result['option_meta']);

			$json_result = \lib\utility\filter::meta_decode([$original_result], null, ['return_object' => true]);
			if(is_object($json_result[0]['option_meta']))
			{
				self::$data = $json_result[0]['option_meta'];
				$json_result = \lib\utility\filter::meta_decode([$original_result], null, ['return_object' => true]);
				self::$data_back = $json_result[0]['option_meta'];
			}
			else
			{
				self::$data = (object) array();
				self::$data_back = (object) array();
			}
		}
		else
		{
			self::$data = (object) array();
			self::$data_back = (object) array();
		}

		return self::$data;
	}

	public static function set($_keys, $_value)
	{
		self::$start;
		$args = func_get_args();
		$object = self::$data;

		if($args && is_array($args[0]))
		{
			$object = $args[1];
			$args = $args[0];
		}
		$arg_value = end($args);
		$keys = array_splice($args, 0 , count($args) -1);

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
		self::$start;

		$args = func_get_args();
		$object = self::$data;

		if($args && is_array($args[0]))
		{
			$object = $args[1];
			$args = $args[0];
		}

		foreach ($args as $key => $value) {
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
		self::$start;

		$args = func_get_args();
		$object = self::$data;

		if($args && is_array($args[0]))
		{
			$object = $args[1];
			$args = $args[0];
		}

		$arg_value = end($args);
		$keys = array_splice($args, 0 , count($args) -1);
		$is_exists = self::get(...$keys);
		$x = $keys;
		if(!$is_exists)
		{
			array_push($keys, [$arg_value]);
			self::set(...$keys);
		}
		else
		{
			if(!is_array($is_exists))
			{
				$is_exists = [$is_exists];
			}
			array_push($is_exists, $arg_value);
			array_push($keys, $is_exists);
			self::set(...$keys);
		}
	}

	public static function remove(...$_keys)
	{
		self::$start;

		$args = func_get_args();
		$object = self::$data;

		if($args && is_array($args[0]))
		{
			$object = $args[1];
			$args = $args[0];
		}

		$end = count($args) - 1;
		foreach ($args as $key => $value) {
			if(!isset($object->$value))
			{
				return false;
			}

			if($key === $end)
			{
				unset($object->$value);
				return true;
			}
			else
			{
				$object = $object->$value;
			}
		}
	}

	public static function save($_user_id = null)
	{
		self::$start;

		$user_id = $_user_id;
		if(!$user_id)
		{
			$user_id = self::$user_id;
		}
		$meta = utf8_encode(json_encode(self::$data, JSON_UNESCAPED_UNICODE));
		$meta = addcslashes($meta, "\\");
		$meta = addcslashes($meta, "'");
		$query = "INSERT INTO options SET
		options.user_id = $user_id,
		options.option_cat = 'user_detail_$user_id',
		options.option_key = 'telegram_session',
		options.option_value = 'session',
		options.option_meta = '$meta'
		ON DUPLICATE KEY UPDATE
		options.option_meta = '$meta'";
		\lib\db::query($query, true, ['resume_on_error' => true]);
	}

	public static function __callStatic($_name, $_args)
	{
		if(preg_match("/^(.*)_back$/", $_name, $name))
		{
			$method_name = $name[1];
			return self::$method_name($_args, self::$data_back);
		}
	}
}
?>