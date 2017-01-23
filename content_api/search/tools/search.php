<?php
namespace content_api\search\tools;
use \lib\utility;
trait search
{
	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function search($_args)
	{
		$meta   = [];
		$search = null;

		if(utility::request("search"))
		{
			$search = utility::request("search");
		}
		else
		{
			$meta['get_last'] = true;
		}

		if(utility::request("my_poll"))
		{
			$meta['my_poll'] = true;
		}

		$get_count = false;
		if(utility::request("get_count"))
		{
			$get_count = true;
			$meta['get_count'] = true;
		}

		if(utility::request("language") && utility\location\languages::check(utility::request("language")))
		{
			$meta['post_language'] = utility::request("language");
		}

		$meta['login'] = $this->user_id;
		$result        = \lib\db\polls::search($search, $meta);
		$tmp_result    = [];

		if(is_array($result) && !$get_count)
		{
			foreach ($result as $key => $value)
			{
				$tmp_result[] = $this->ready_poll($value);
			}
		}
		elseif($get_count)
		{
			return (int) $result;
		}

		return $tmp_result;
	}
}

?>