<?php
namespace lib\db\polls\insert;
use \lib\utility;
use \lib\db;

trait reset
{
	/**
	 * reset poll
	 * in PUT method
	 */
	protected static function reset()
	{
		$reset_value =
		[
			'post_language'    => null,
			'post_title'       => '‌',
			'post_slug'        => '‌',
			'post_url'         => '$/'. utility\shortURL::encode(self::$poll_id),
			'post_content'     => null,
			'post_status'      => 'draft',
			'post_parent'      => null,
			'post_meta'        => null,
			'post_publishdate' => null,
			'post_privacy' 	   => 'private',
			'post_survey'      => null,
		];
		self::update($reset_value, self::$poll_id);
		db\pollopts::set_status(self::$poll_id); // set all opts as disable
		$where =
		[
			'post_id'    => self::$poll_id,
			'option_cat' => 'poll_'. self::$poll_id,
			'user_id'    => null,
		];
		$arg = ['option_status' => 'disable'];
		db\options::update_on_error($arg, $where);
	}
}
?>