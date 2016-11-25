<?php
namespace lib\utility\answers;

trait get
{

	/**
	 * get post id and return opt of this post
	 *
	 * @param      <type>  $_poll_id  The post identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id)
	{
		$query = "
				SELECT
					*
				FROM
					options
				WHERE
					post_id = $_poll_id AND
					option_cat LIKE 'poll_{$_poll_id}' AND
					option_key LIKE 'opt%'  AND
					user_id IS NULL AND
					option_status = 'enable'
				-- answers::get()
				";
		$result = \lib\db\options::select($query, "get");
		return \lib\utility\filter::meta_decode($result);
	}
}
?>