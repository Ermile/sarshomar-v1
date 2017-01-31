<?php
namespace content_api\v1\poll\search\tools;
use \lib\utility;
use \lib\debug;

trait search
{
	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function poll_search($_args = [])
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

		$from = utility::request("from");
		$to   = utility::request("to");

		if(!preg_match("/^\d+$/", $from))
		{
			if(is_null($from))
			{
				$from = 0;
			}
			else
			{
				return debug::error(T_("Invalid parameter from, from must be integer"), 'from', 'arguments');
			}
		}
		else
		{
			$from = (int) $from;
		}
		if(!preg_match("/^\d+$/", $to))
		{
			if(is_null($to))
			{
				$to = $from + 10;
			}
			else
			{
				return debug::error(T_("Invalid parameter to, to must be integer"), 'to', 'arguments');
			}
		}
		else
		{
			$to = (int) $to;
		}

		if($from > $to)
		{
			return debug::error(T_("Parameter 'from' must be less than parameter 'to'"), 'from', 'arguments');
		}

		if(utility::request("language"))
		{
			if(!utility\location\languages::check(utility::request("language")))
			{
				return debug::error(T_("Invalid parameter language"), 'language', 'arguments');
			}
			else
			{
				$meta['post_language'] = utility::request("language");
			}
		}

		$meta['login']       = $this->user_id;
		$meta['api_mode']    = $this->api_mode;
		$meta['start_limit'] = $from;
		$meta['limit']       = $to - $from;
		$result              = \lib\db\polls::search($search, $meta);
		$tmp_result          = [];
		$tmp_result['data']  = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$tmp_result['data'][] = $this->poll_ready($value);
			}
		}

		$tmp_result['from']  = $from;
		$tmp_result['to']    = (int) $from  + count($tmp_result['data']);
		$tmp_result['total'] = (int) \lib\storage::get_total_record();

		return $tmp_result;
	}
}

?>