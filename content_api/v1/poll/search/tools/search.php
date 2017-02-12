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

		$in_me = false;

		if(utility::request("in"))
		{

			if(is_string(utility::request('in')))
			{
				$in_list = ['sarshomar', 'me', 'article'];
				if(!in_array(utility::request('in'), $in_list))
				{
					return debug::error(T_("Invalid parameter 'in' "), 'in', 'arguments');
				}
				$meta['in'] = utility::request("in");
			}
			elseif(is_array(utility::request('in')))
			{
				if(in_array('all', utility::request('in')))
				{
					return debug::error(T_("Can not set all in array request"), 'in', 'arguments');
				}
				$meta['in'] = utility::request("in");
			}
			else
			{
				return debug::error(T_("Invalid parameter in as :type", ['type' => gettype(utility::request('in'))]), 'in', 'arguments');
			}

			if((is_array(utility::request('in')) && in_array('me', utility::request('in'))) || utility::request('in')  == 'me' )
			{
				$in_me = true;
			}

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
			if(is_string(utility::request('language')))
			{
				if(!utility\location\languages::check(utility::request("language")))
				{
					return debug::error(T_("Invalid parameter language"), 'language', 'arguments');
				}
				$meta['post_language'] = utility::request("language");
			}
			elseif(is_array(utility::request('language')))
			{
				foreach (utility::request('language') as $key => $value)
				{
					if(!utility\location\languages::check($value))
					{
						return debug::error(T_("Invalid parameter language"), 'language', 'arguments');
					}
				}
				$meta['post_language'] = ['IN', "('". implode("','", utility::request("languages")). "')"];
			}
			else
			{
				return debug::error(T_("Invalid parameter language as :type", ['type' => gettype(utility::request('language'))]), 'language', 'arguments');
			}
		}


		if(utility::request("status"))
		{
			if(!$in_me)
			{
				return debug::error(T_("You can set status in your poll, in:me"), 'status', 'arguments');
			}

			$status_list =
			[
				'stop',
				'pause',
				'trash',
				'publish',
				'draft',
				'awaiting',
			];

			if(is_string(utility::request('status')))
			{
				if(!in_array(utility::request('status'), $status_list))
				{
					return debug::error(T_("Invalid parameter status"), 'status', 'arguments');
				}
				$meta['post_status'] = utility::request("status");
			}
			elseif(is_array(utility::request('status')))
			{
				foreach (utility::request('status') as $key => $value)
				{
					if(!in_array($value, $status_list))
					{
						return debug::error(T_("Invalid status :status", ['status' => $value]), 'status', 'arguments');
					}
				}
				$meta['post_status'] = ['IN', "('". implode("','", utility::request("status")). "')"];
			}
			else
			{
				return debug::error(T_("Invalid parameter status as :type", ['type' => gettype(utility::request('status'))]), 'status', 'arguments');
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