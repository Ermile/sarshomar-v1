<?php
namespace lib\db\polls;

trait update
{

	/**
	 * update the poll url
	 * get the poll id and poll title
	 * set url = $/[encode of poll id]/[title whit replace \s to _ ]
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_title    The title
	 */
	public static function update_url($_poll_id, $_title, $_update = true)
	{
		$short_url = \lib\utility\shortURL::encode($_poll_id);
		// $title = preg_replace("/[\n\t\s\,\-\(\)\!\@\#\$\%\^\&\/\.\?\<\>\|\{\}\[\]\"\'\:\;\*]/", "_", $title);
		$_title = str_replace(" ", "_", $_title);
		$url = '$/' . $short_url . '/'. $_title;
		// default the update is true
		// we update the poll url
		if($_update)
		{
			return \lib\db\polls::update(['post_url' => $url], $_poll_id);
		}
		else
		{
			// update is false then we return the url
			// for update another place
			return $url;
		}

	}


	/**
	 * update polls as post record
	 *
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_id) {
		return \lib\db\posts::update($_args, $_id);
	}


	/**
	 * get the list of answers and update the poll meta
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function update_answer_in_meta($_poll_id)
	{
		$meta = self::get_poll_meta($_poll_id);
		$answers = \lib\utility\answers::get($_poll_id);
		$new_meta = [];
		foreach ($answers as $key => $value)
		{
			$type = '';
			if(isset($value['option_meta']['type']))
			{
				$type = $value['option_meta']['type'];
			}

			$new_meta[] =
			[
				'key'  => $value['option_key'],
				'txt'  => $value['option_value'],
				'type' => $type
			];
		}
		return self::replace_meta(['opt' => $new_meta] , $_poll_id);
	}


	/**
	 * remove index from meta
	 *
	 * @param      <type>  $_field_meta  The field meta
	 * @param      <type>  $_poll_id     The poll identifier
	 */
	public static function replace_meta($_field_meta, $_poll_id)
	{

		$meta = self::get_poll_meta($_poll_id);
		if(!is_array($meta))
		{
			$meta = [];
		}

		$find_replace = false;

		foreach ($_field_meta as $key => $value)
		{
			if(isset($meta[$key]))
			{
				if($value != $meta[$key])
				{
					$find_replace = true;
					$meta[$key] = $value;
				}
			}
		}
		if(!$find_replace)
		{
			return true;
		}

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		$meta_query = " '$meta' ";

		$query =
		"
			UPDATE
				posts
			SET
				post_meta  = $meta_query
			WHERE
				posts.id = $_poll_id
			-- polls::remove_index_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * remove index from meta
	 *
	 * @param      <type>  $_field_meta  The field meta
	 * @param      <type>  $_poll_id     The poll identifier
	 */
	public static function remove_index_meta($_field_meta, $_poll_id)
	{

		$meta = self::get_poll_meta($_poll_id);
		if(!is_array($meta))
		{
			$meta = [];
		}

		$find_remove = false;

		foreach ($_field_meta as $key => $value) {
			if(isset($meta[$key]))
			{
				if($value === null || $value == $meta[$key])
				{
					$find_remove = true;
					unset($meta[$key]);
				}
			}
		}
		if(!$find_remove)
		{
			return true;
		}

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		$meta_query = " '$meta' ";


		$query =
		"
			UPDATE
				posts
			SET
				post_meta  = $meta_query
			WHERE
				posts.id = $_poll_id
			-- polls::remove_index_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * Appends a meta.
	 * meta of posts table is a json field
	 * if this field is full we merge new value and old value of this field
	 *
	 * @param      <type>  $_field_meta  The field meta
	 * @param      <type>  $_poll_id     The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function merge_meta($_field_meta, $_poll_id)
	{
		if(defined("mysql_json"))
		{
			$meta = json_encode($_field_meta, JSON_UNESCAPED_UNICODE);
			$meta_query = " JSON_MERGE(JSON_EXTRACT(posts.post_meta, '$'), '$meta')";
		}
		else
		{
			$meta = self::get_poll_meta($_poll_id);
			if(!is_array($meta))
			{
				$meta = [];
			}
			$meta = array_merge($meta, $_field_meta);

			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
			$meta_query = " '$meta' ";
		}

		$query =
		"
			UPDATE
				posts
			SET
				post_meta  = $meta_query
			WHERE
				posts.id = $_poll_id
			-- polls::merge_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>