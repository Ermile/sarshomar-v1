<?php
namespace content_admin\knowledge;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	use \content_api\v1\home\tools\ready;
	use \content_api\v1\poll\search\tools\search;
	use \content_api\v1\poll\tools\get;
	use \content_api\v1\poll\status\tools\get;
	use \content_api\v1\poll\status\tools\set;


	/**
	 * Posts a knowledge.
	 */
	public function post_knowledge()
	{
		if(utility::post('spam-word') != '')
		{
			\lib\db\words::set_status(utility::post('spam-word'), utility::post("status"));
			\lib\debug::true(T_("Words Status change"));
			return;
		}

		if(utility::post("vip-value") != '')
		{
			$poll_id = utility\shortURL::decode(utility::post("id"));
			$vip_value = utility::post("vip-value");
			\lib\db\ranks::plus($poll_id, 'vip', $vip_value, ['replace' => true]);
			\lib\debug::true(T_("Vip ranks of post saved"));
			return ;
		}

		$code   = utility::post("id");
		$id     = utility\shortURL::decode($code);
		$status = utility::post("status");

		if($status == 'publish')
		{
			$update = ['post_status' => 'publish'];
			// dave start date and end date in post_meta
			$update_post_meta = \lib\db\polls::merge_meta(['review' => 'ok'], $id);
		}
		else
		{
			$all_status = \content_api\v1\poll\status\tools\set::$all_status;

			if(!in_array($status, $all_status))
			{
				return debug::error(T_("Invalid parameter status"));
			}
			$update = ['post_status' => $status];
			$update_post_meta = \lib\db\polls::remove_index_meta(['review' => 'ok'], $id);
		}

		$result = \lib\db\polls::update($update, $id);
		if($result)
		{
			\lib\debug::true(T_("Post status updated"));
		}
		else
		{
			\lib\debug::error(T_("Error in updating post"));
		}
		return;

		utility::set_request_array(['id' => $id]);
		$this->user_id = $this->login('id');
		$availible = $this->poll_status();
		if(isset($availible['available']) && is_array($availible['available']))
		{
			if(in_array($status, $availible['available']))
			{
				utility::set_request_array(['id' => $id, 'status' => $status]);
				$this->poll_set_status();
				return;
			}
			else
			{
				debug::error(T_("Can not set this status to this poll"));
				return false;
			}
		}
		else
		{
			debug::error(T_("Can not found availible status"));
		}

		return;


	}

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		$match = $_args;
		unset($_args->match->url);
		unset($_args->method);
		unset($_args->match->property);
		$match  = $match->match;
		$status = null;
		if(isset($match->status[0]))
		{
			$status = $match->status[0];
		}

		$sarshomar = false;
		if(isset($match->sarshomar[0]) && $match->sarshomar[0])
		{
			$sarshomar = true;
		}

		$filter = [];

		foreach ($match as $key => $value)
		{
			if(is_array($value) && isset($value[0]))
			{
				$value = $value[0];
			}
			if(\lib\db\filters::support_filter($key))
			{
				$filter[$key] = $value;
			}
		}
		$this->user_id = $this->login('id');
		$request                = [];
		$request['in']          = 'all';
		if($status)
		{
			$request['status'] = $status;
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
			$request['status'] = implode(" ", $status_list);
		}
		if($sarshomar)
		{
			// $request['in'] = "sarshomar";
		}

		$request['order']       = 'desc';
		utility::set_request_array($request);
		$options =
		[
			'get_tags'			 => false,
			'get_filter'         => false,
			'get_opts'           => true,
			'get_options'        => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'run_options'        => false,
			'check_is_my_poll'   => false,
			'debug'				 => false,
		];

		$poll_list = $this->poll_search($options);

		return $poll_list;
	}

	public function count_poll_status()
	{
		return \lib\db\polls::count_poll_status();
	}

}
?>