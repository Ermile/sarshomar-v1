<?php
namespace content_api\v1\poll\search\tools;
use \lib\utility;
use \lib\debug;

trait search
{
	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_options  The arguments
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function poll_search($_options = [])
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

		$in_me      = false;
		$admin_mode = false;

		if(\content_api\v1\home\tools\api_options::check_api_permission('admin', 'admin', 'view'))
		{
			$admin_mode = true;
		}
		else
		{
			if(utility::request('admin'))
			{
				debug::error(T_("Invalid parameter admin"), 'admin', 'arguments');
				return false;
			}
		}

		if(utility::request("in"))
		{
			$split = explode(' ', utility::request('in'));
			if(count($split) === 1 && isset($split[0]))
			{
				$split = $split[0];
			}

			if(is_string($split))
			{
				$in_list = ['sarshomar', 'me', 'article', 'all'];

				if($admin_mode)
				{
					array_push($in_list, 'people');
				}

				if(!in_array($split, $in_list))
				{
					return debug::error(T_("Invalid parameter 'in' "), 'in', 'arguments');
				}
				$meta['in'] = $split;
			}
			elseif(is_array($split))
			{
				if(in_array('all', $split))
				{
					return debug::error(T_("You can not set 'all' parameter in array 'in'"), 'in', 'arguments');
				}
				$meta['in'] = $split;
			}
			else
			{
				return debug::error(T_("The 'in' parameter type is invalid"), 'in', 'arguments');
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
			$language = explode(' ', utility::request('language'));
			if(count($language) === 1)
			{
				if(!utility\location\languages::check(utility::request("language")))
				{
					return debug::error(T_("Invalid parameter language"), 'language', 'arguments');
				}
				$meta['post_language'] = utility::request("language");
			}
			elseif(count($language) > 1)
			{
				foreach ($language as $key => $value)
				{
					if(!utility\location\languages::check($value))
					{
						return debug::error(T_("Invalid parameter language"), 'language', 'arguments');
					}
				}
				$meta['post_language'] = ['IN', "('". implode("','", $language). "')"];
			}
			else
			{
				return debug::error(T_("Invalid parameter language as :type", ['type' => gettype(utility::request('language'))]), 'language', 'arguments');
			}
		}


		if(utility::request("status"))
		{
			if(!$in_me && !self::check_api_permission('admin'))
			{
				return debug::error(T_("You can not set status and search in all polls"), 'status', 'arguments');
			}

			$status = explode(' ', utility::request('status'));
			if(self::check_api_permission('admin'))
			{
				$status_list =
				[
					'stop',
					'pause',
					'trash',
					'publish',
					'draft',
					'deleted',
					'awaiting',
					'filtered',
					'blocked',
					'spam',
					'violence',
					'pornography',
					'schedule',
					'expired',
					'enable',
					'disable',
					'other',
				];
			}
			else
			{
				$status_list =
				[
					'stop',
					'pause',
					'trash',
					'publish',
					'draft',
					'awaiting',
				];

			}
			if(count($status) === 1)
			{
				if(!in_array($status[0], $status_list))
				{
					return debug::error(T_("Invalid parameter status"), 'status', 'arguments');
				}
				$meta['post_status'] = $status[0];
			}
			elseif(count($status) > 1)
			{
				foreach ($status as $key => $value)
				{
					if(!in_array($value, $status_list))
					{
						return debug::error(T_("Invalid status :status", ['status' => $value]), 'status', 'arguments');
					}
				}
				$meta['post_status'] = ['IN', "('". implode("','", $status). "')"];
			}
			else
			{
				return debug::error(T_("Invalid parameter status as :type", ['type' => gettype(utility::request('status'))]), 'status', 'arguments');
			}
		}

		if(utility::isset_request('sort'))
		{
			$avalible_sort = ['id', 'rank', 'vote', 'date', 'comment', 'title'];
			if(!in_array(utility::request('sort'), $avalible_sort))
			{
				debug::error(T_("Invalid parameter sort"), 'sort', 'arguments');
				return false;
			}
			$sort_field = 'id';
			switch (utility::request('sort'))
			{
				case 'id':
					$sort_field = 'posts.id';
					break;
				case 'rank':
					$sort_field = 'posts.post_rank';
					break;
				case 'vote':
					$sort_field = 'count_vote';
					break;
				case 'date':
					$sort_field = 'posts.post_createdate';
					break;
				case 'comment':
					$sort_field = 'count_comment';
					break;
				case 'title':
					$sort_field = 'posts.post_title';
					break;
				default:
					$sort_field = 'posts.id';
					break;
			}
			$meta['sort']        = $sort_field;
		}


		if(utility::isset_request('order'))
		{
			$avalible_order = ['asc', 'desc', 'ASC', 'DESC'];
			if(!in_array(utility::request('order'), $avalible_order))
			{
				debug::error(T_("Invalid parameter order"), 'order', 'arguments');
				return false;
			}
			$meta['order']       = utility::request('order');
		}

		$meta['login']       = $this->user_id;
		$meta['api_mode']    = $this->api_mode;
		$meta['start_limit'] = $from;
		$meta['limit']       = $to - $from;

		if(\content_api\v1\home\tools\api_options::check_api_permission('admin', 'admin'))
		{
			$meta['admin']       = utility::request('admin') ? true : false;
			if($meta['admin'])
			{
				$meta['limit']   = utility::isset_request('admin') ? utility::request('limit') : $meta['limit'];
			}
		}

		$result              = \lib\db\polls::search($search, $meta);
		$tmp_result          = [];
		$tmp_result['data']  = [];

		$default_options =
		[
			'get_tags'			 => false,
			'get_filter'         => false,
			'get_opts'           => false,
			'get_options'        => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'run_options'        => false,
			'check_is_my_poll'   => false,
			'debug'				 => false,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$temp = $this->poll_ready($value, $_options);
				if($temp)
				{
					$tmp_result['data'][] = $temp;
				}
			}
		}
		$tmp_result['from']  = $from;
		$tmp_result['to']    = (int) $from  + count($tmp_result['data']);
		$tmp_result['total'] = (int) \lib\storage::get_total_record();
		return $tmp_result;
	}
}

?>