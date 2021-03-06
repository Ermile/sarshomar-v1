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

		if(utility::post("type"))
		{
			switch (utility::post('type'))
			{
				case 'homepage':
					$is_checked = utility::post("checked") === 'true' ? true : false;
					$args = ['poll_id' => $id, 'checked' => $is_checked];
					$this->homepage($args);

					break;

				default:
					debug::error(T_("Invalid parameter type"));
					break;
			}
			return;
		}
		// change user dashboard data
		$poll                  = \lib\db\polls::get_poll($id);
		$change_dashboard_data = [];

		if(isset($poll['user_id']))
		{
			$change_dashboard_data['user_id'] = $poll['user_id'];
		}

		if(isset($poll['status']))
		{
			$change_dashboard_data['old_status'] = $poll['status'];
		}

		$change_dashboard_data['new_status'] = $status;

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
			\content_api\v1\poll\status\tools\set::change_dashboard($change_dashboard_data);
			\lib\debug::true(T_("Post status updated on :status", ['status' => T_($status)]));

		}
		else
		{
			\lib\debug::error(T_("Error in updating post"));
		}
	}

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		$request           = [];
		$request['in']     = 'all';
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

		if(isset($match->sarshomar[0]) && $match->sarshomar[0])
		{
			$request['in'] = 'sarshomar';
		}

		if(isset($match->sarshomar) && is_array($match->sarshomar) &&  array_key_exists('0', $match->sarshomar) && !$match->sarshomar[0])
		{
			$request['in'] = 'people';
		}

		if(isset($match->status[0]) && $match->status[0])
		{
			$request['status'] = $match->status[0];
		}

		if(isset($match->sort[0]) && $match->sort[0])
		{
			$request['sort'] = $match->sort[0];
		}

		if(isset($match->order[0]) && $match->order[0])
		{
			$request['order'] = $match->order[0];
		}

		$search = null;
		if(isset($match->search[0]))
		{
			$search = $match->search[0];
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
		$request['admin']  = true;
		$request['limit']  = 15;
		$request['search'] = $search;
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

		if(!isset($request['order']))
		{
			$request['order']       = 'desc';
		}

		utility::set_request_array($request);
		$options =
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

		$poll_list = $this->poll_search($options);
		$poll_ids = [];
		$homepage = [];
		if(isset($poll_list['data']) && is_array($poll_list['data']))
		{
			$poll_codes = array_column($poll_list['data'], 'id');
			$poll_ids = array_map(function($_a){ return utility\shortURL::decode($_a);}, $poll_codes);
			if(!empty($poll_ids))
			{
				// load home page
				$poll_ids = implode(',', $poll_ids);
				$query =
				"
					SELECT
						post_id, option_status
					FROM options
					WHERE
						`post_id`      IN ($poll_ids) AND
						`option_cat`   = 'homepage' AND
						`option_key`   = 'chart' AND
						`option_value` IN ($poll_ids)
				";
				$homepage = \lib\db::get($query, ['post_id', 'option_status']);
				if(is_array($homepage) && !empty($homepage))
				{
					$temp_homepage = [];
					foreach ($homepage as $key => $value)
					{
						if($value == 'enable')
						{
							array_push($temp_homepage, $key);
						}
					}
					$homepage = $temp_homepage;
				}
				else
				{
					$homepage = [];
				}
				// load image mode
				// $query_image =
				// "
				// 	SELECT options.post_id AS `post_id`, 'title_attachment' AS `type`
				// 	FROM options
				// 	WHERE
				// 	options.post_id IN ($poll_ids) AND
				// 	options.option_cat    = CONCAT('poll_', options.post_id) AND
				// 	options.option_key  = 'title_attachment' AND
				// 	options.option_status = 'enable'
				// 	UNION
				// 	SELECT pollopts.post_id AS `post_id`, 'opt_attachment' AS `type`
				// 	FROM pollopts
				// 	WHERE
				// 	pollopts.post_id IN ($poll_ids) AND pollopts.attachment_id IS NOT NULL AND
				// 	pollopts.status = 'enable'
				// ";
				// $have_media = \lib\db::get($query_image, ['post_id', 'type']);
				// $poll_list['attachment'] = $have_media;
			}
		}
		$poll_list['homepage'] = $homepage;

		return $poll_list;

	}

	public function count_poll_status()
	{
		return \lib\db\polls::count_poll_status();
	}


	/**
	 * check homepage feaucher and set in options table
	 */
	public function homepage($_options = [])
	{
		$default_options =
		[
			'checked' => true,
			'poll_id' => null,
		];
		$_options = array_merge($default_options, $_options);

		// disable if home page exits
		$disable = ['option_status' => 'disable'];
		$enable  = ['option_status' => 'enable'];
		$where =
		[
			'post_id'      => $_options['poll_id'],
			'option_cat'   => 'homepage',
			'option_key'   => 'chart',
			'option_value' => $_options['poll_id'],
			'limit'        => 1,
		];

		$result = \lib\db\options::get($where);
		unset($where['limit']);
		$query_result = false;
		if($_options['checked'])
		{
			if(!empty($result))
			{
				$query_result = \lib\db\options::update_on_error($enable, $where);
			}
			else
			{
				$query_result = \lib\db\options::insert($where);
			}
			debug::true(T_("Poll show in home page"));
		}
		else
		{
			if(!empty($result))
			{
				$query_result = \lib\db\options::update_on_error($disable, $where);
			}
			debug::true(T_("Remove poll from home page"));
		}
	}
}
?>