<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait ready
{

	/**
	 * ready poll record to show
	 * encode id
	 * remove null index
	 * some thing more...
	 * @param      <type>  $_poll_data  The poll data
	 * @param      array   $_options    The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function poll_ready($_poll_data, $_options = [])
	{
		$default_options =
		[
			'get_tags'			 => true,
			'get_filter'         => false,
			'get_opts'           => false,
			'get_options'        => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'run_options'        => true,
			'check_is_my_poll'   => false,
		];
		// merge settings
		$_options = array_merge($default_options, $_options);

		$poll_id = false;

		// encode id
		if(array_key_exists('id', $_poll_data))
		{
			$poll_id          = $_poll_data['id'];
			$_poll_data['id'] = shortURL::encode($_poll_data['id']);
		}

		// check id
		if(!$poll_id)
		{
			return debug::error(T_("Poll not found"), "id", 'arguments');
		}

		$my_poll = false;
		if(array_key_exists('user_id', $_poll_data))
		{
			if($this->user_id == $_poll_data['user_id'])
			{
				$my_poll = true;
			}
		}

		if($_options['check_is_my_poll'] && !$my_poll)
		{
			return debug::error(T_("Can not access to load this poll (this is not your poll)"), "id", 'permission');
		}

		if(array_key_exists('status', $_poll_data))
		{
			$msg = null;
			$permission_load_poll = false;
			switch ($_poll_data['status'])
			{

				case 'draft':
				case 'trash':
				case 'awaiting':
					if($my_poll)
					{
						$permission_load_poll = true;
					}
					break;

				case 'publish':
				case 'stop':
				case 'pause':
				case 'schedule':
				case 'expired':
					$permission_load_poll = true;
					break;

				case 'deleted':
				case 'filtered':
				case 'blocked':
				case 'spam':
				case 'violence':
				case 'pornography':
				case 'disable':
					$permission_load_poll = false;
					if($my_poll)
					{
						$msg = T_("(The poll is :status)", ['status' => $_poll_data['status']]);
					}
					break;

				case 'other':
				case 'enable':
				default:
					$permission_load_poll = false;
					break;
			}

			if(!$permission_load_poll)
			{
				return debug::error(T_("Can not access to load this poll :msg",['msg' => $msg]), "id", 'permission');
			}
		}
		else
		{
			return debug::error(T_("Invalid parameter status"), 'status', 'system');
		}

		foreach ($_poll_data as $key => $value)
		{
			if($key == 'id')
			{
				continue;
			}

			if(is_numeric($value))
			{
				$_poll_data[$key] = (float) $value;
			}

			if($value === null || $value === '')
			{
				$_poll_data[$key] = null;
			}
		}
		$host = Protocol."://" . \lib\router::get_root_domain();

		if(array_key_exists('title', $_poll_data) && $_poll_data['title'] == '‌')
		{
			$_poll_data['title'] = '';
		}

		if(array_key_exists('slug', $_poll_data) && $_poll_data['slug'] == '‌')
		{
			$_poll_data['slug'] = '';
		}

		if(array_key_exists('url', $_poll_data) && $_poll_data['url'] == '‌')
		{
			$_poll_data['url'] = '';
		}

		// encode user id
		if(array_key_exists('user_id', $_poll_data))
		{
			$_poll_data['user_id'] = shortURL::encode($_poll_data['user_id']);
		}

		// encode parent
		if(array_key_exists('parent', $_poll_data))
		{
			$_poll_data['parent'] = shortURL::encode($_poll_data['parent']);
		}

		if(array_key_exists('content', $_poll_data))
		{
			$_poll_data['description'] = $_poll_data['content'];
		}

		// encode suervey
		if(array_key_exists('survey', $_poll_data))
		{
			$_poll_data['survey'] = shortURL::encode($_poll_data['survey']);
		}

		// change have_score field
		if(array_key_exists('have_score', $_poll_data))
		{
			if($_poll_data['have_score'] == '1')
			{
				$_poll_data['have_score'] = true;
			}
			else
			{
				$_poll_data['have_score'] = false;
			}
		}

		// change is_answered field
		if(array_key_exists('is_answered', $_poll_data))
		{
			if($_poll_data['is_answered'] == '1')
			{
				$_poll_data['is_answered'] = true;
			}
			else
			{
				$_poll_data['is_answered'] = false;
			}
		}

		// change my_like field
		if(array_key_exists('my_like', $_poll_data))
		{
			if($_poll_data['my_like'] == '1')
			{
				$_poll_data['my_like'] = true;
			}
			else
			{
				$_poll_data['my_like'] = false;
			}
		}

		// change my_fav field
		if(array_key_exists('my_fav', $_poll_data))
		{
			if($_poll_data['my_fav'] == '1')
			{
				$_poll_data['my_fav'] = true;
			}
			else
			{
				$_poll_data['my_fav'] = false;
			}
		}

		// change have_true_answer field
		if(array_key_exists('have_true_answer', $_poll_data))
		{
			if($_poll_data['have_true_answer'] == '1')
			{
				$_poll_data['have_true_answer'] = true;
			}
			else
			{
				$_poll_data['have_true_answer'] = false;
			}
		}

		// change sarshomar field
		if(array_key_exists('sarshomar', $_poll_data) && $_poll_data['sarshomar'])
		{
			$_poll_data['sarshomar'] = true;
		}
		else
		{
			$_poll_data['sarshomar'] = false;
		}


		if(isset($_poll_data['meta']['summary']))
		{
			$_poll_data['summary'] = $_poll_data['meta']['summary'];
			unset($_poll_data['meta']['summary']);
		}

		unset($_poll_data['meta']);

		$cat = \lib\db\terms::usage($poll_id, [], 'cat', 'sarshomar');

		if($cat)
		{
			if(isset($cat[0]['id']))
			{
				$_poll_data['options']['cat'] = shortURL::encode($cat[0]['id']);
			}
		}

		// get opts of poll
		if($_options['get_opts'] && $poll_id)
		{
			$custom_field =
			[
				'id',
				'type',
				'title',
				'subtype',
				'true',
				'groupscore',
				'desc',
				'score',
				'attachment_id',
				'attachmenttype',
			];

			$answers = \lib\db\pollopts::get($poll_id, $custom_field, true);

			$show_answers = [];
			foreach ($answers as $key => $value)
			{
				$show_key = $key + 1;

				if($this->access('u','complete_profile', 'admin'))
				{
					$opt_profile = [];
					if(isset($value['id']))
					{
						$profile = \lib\db\terms::usage($value['id'], [], 'profile', 'sarshomar');

						if($profile && is_array($profile))
						{
							foreach ($profile as $k => $v)
							{
								if(isset($v['id']))
								{
									$opt_profile[$k]['id'] = shortURL::encode($v['id']);
								}

								if(isset($v['term_title']))
								{
									$opt_profile[$k]['title'] = $v['term_title'];
								}
							}
						}
					}
					if(!empty($opt_profile))
					{
						$_poll_data['profile'] = true;
					}
					$answers[$key]['profile'] = $opt_profile;
				}

				// unset($answers[$key]['id']);

				if(isset($value['true']) && $value['true'] == '1')
				{
					$show_answers[$show_key]['true'] = true;
				}
				else
				{
					$show_answers[$show_key]['true'] = false;
				}

				if(isset($value['attachment_id']) && $value['attachment_id'])
				{
					$attachment = \lib\db\polls::get_poll($value['attachment_id']);
					$url = null;
					if(isset($attachment['meta']['url']))
					{
						$answers[$key]['file']['id'] = \lib\utility\shortURL::encode($value['attachment_id']);
						$answers[$key]['file']['url'] = $host. '/'. $attachment['meta']['url'];
					}
				}

				if(isset($value['groupscore']) && $value['groupscore'])
				{
					$_poll_data['advance_score'] = true;
				}

				unset($answers[$key]['attachment_id']);
				unset($answers[$key]['id']);

				$show_answers[$show_key] = array_filter($answers[$key]);
			}

			$_poll_data['answers'] = $show_answers;
		}

		// get filters of poll
		if($_options['get_filter'] && $poll_id)
		{
			$filters               = utility\postfilters::get_filter($poll_id);
			$filters['count']     = \lib\db\ranks::get($poll_id, 'member');
			$filters               = array_filter($filters);
			$_poll_data['filters'] = $filters;
		}

		if(($_options['get_public_result'] || $_options['get_advance_result']) && $poll_id)
		{
			$poll_result = [];
			$poll_result_raw = utility\stat_polls::get_result($poll_id);
			if($_options['get_public_result'])
			{
				if(isset($poll_result_raw['valid']['result']) && is_array($poll_result_raw['valid']['result']))
				{
					foreach ($poll_result_raw['valid']['result'] as $key => $value)
					{
						if(substr($key, 0,4) == 'opt_')
						{
							$poll_result['total']['valid'][] = ['key' => substr($key, 4), 'value' => $value];
						}
					}

				}

				if(isset($poll_result_raw['invalid']['result']) && is_array($poll_result_raw['invalid']['result']))
				{
					foreach ($poll_result_raw['invalid']['result'] as $key => $value)
					{
						if(substr($key, 0,4) == 'opt_')
						{
							$poll_result['total']['invalid'][] = ['key' => substr($key, 4), 'value' => $value];
						}
					}
				}
			}

			if($_options['get_advance_result'] && is_array($poll_result_raw))
			{
				$advance_result = [];

				if(isset($poll_result_raw['valid']) && is_array($poll_result_raw['valid']))
				{
					foreach ($poll_result_raw['valid'] as $key => $value)
					{
						if($key !== 'result')
						{
							if(is_array($value))
							{
								foreach ($value as $k => $v)
								{
									if(substr($k, 0,4) == 'opt_')
									{
										$poll_result['advance_stats']['valid'][$key][] = ['key' => substr($k, 4), 'value' => $v];
									}
								}
							}
						}
					}
				}

				if(isset($poll_result_raw['invalid']) && is_array($poll_result_raw['invalid']))
				{
					foreach ($poll_result_raw['invalid'] as $key => $value)
					{
						if($key !== 'result')
						{
							if(is_array($value))
							{
								foreach ($value as $k => $v)
								{
									if(substr($k, 0,4) == 'opt_')
									{
										$poll_result['advance_stats']['invalid'][$key][] = ['key' => substr($k, 4), 'value' => $v];
									}
								}
							}
						}
					}
				}
			}
			$_poll_data['stats'] = $poll_result;
		}


		$post_meta = \lib\db\posts::get_post_meta($poll_id);
		if(is_array($post_meta))
		{

			if($_options['get_options'])
			{
				$temp_options = array_column($post_meta, 'option_value', 'option_key');
				$show_options = [];
				foreach ($temp_options as $key => $value)
				{

					if($value == '1')
					{
						$show_options[$key] = true;
					}

					if($key === 'multi_min' || $key === 'multi_max')
					{
						$show_options['multi'][substr($key,6)] = (int) $value;
					}

				}
				if(!empty($show_options))
				{
					if(isset($_poll_data['options']))
					{
						$_poll_data['options'] = array_merge($_poll_data['options'], $show_options);
					}
					else
					{
						$_poll_data['options'] = $show_options;
					}
				}
			}

			$poll_tree_answer = [];
			$poll_articles    = [];

			foreach ($post_meta as $key => $value)
			{
				if(isset($value['option_key']) && preg_match("/^tree/", $value['option_key']))
				{
					if(isset($value['option_value']))
					{
						array_push($poll_tree_answer, $value['option_value']);
					}
				}

				if(isset($value['option_key']) && preg_match("/^articles/", $value['option_key']))
				{
					if(isset($value['option_value']))
					{
						array_push($poll_articles, shortURL::encode($value['option_value']));
					}
				}

				if(isset($value['option_key']) && $value['option_key'] == 'title_attachment')
				{
					if(isset($value['option_meta']['url']))
					{
						$_poll_data['file'] = $value['option_meta']['url'];
					}
				}
			}

			$_poll_data['articles'] = $poll_articles;

			if(!empty($poll_tree_answer) && isset($_poll_data['parent']))
			{
				$_poll_data['tree'] = [];
				// $_poll_data_tree = utility\poll_tree::get($poll_id);

				// if($_poll_data_tree && is_array($_poll_data_tree))
				// {
				// 	$opt = array_column($_poll_data_tree, 'value');
					$_poll_data['tree']['parent']  = $_poll_data['parent'];
					$_poll_data['tree']['title']   = \lib\db\polls::get_poll_title(shortURL::decode($_poll_data['parent']));
					$_poll_data['tree']['answers'] = $poll_tree_answer;
					unset($_poll_data['parent']);
				// }
			}

			$post_meta_key = array_column($post_meta, 'option_key');

			if(in_array('random_sort', $post_meta_key) && $_options['run_options'])
			{
				if(array_key_exists('answers', $_poll_data) && is_array($_poll_data['answers']))
				{
					$new  = [];
					$keys = array_keys($_poll_data['answers']);
			        shuffle($keys);
			        ;
			        foreach($keys as $key => $value)
			        {
			        	$new[$value] = $_poll_data['answers'][$value];
			        }
			        $_poll_data['answers'] = $new;

				}

				// unset($_poll_data['options']['random_sort']);
			}

			if(in_array('hidden_result', $post_meta_key) && $_options['run_options'])
			{
				unset($_poll_data['stats']);
				// unset($_poll_data['options']['hidden_result']);
			}

			$brand = [];

			foreach ($post_meta as $key => $value)
			{
				if($value['option_key'] == 'brand')
				{
					$brand['title'] = $value['option_value'];
					if(isset($value['option_meta']['url']))
					{
						$brand['url'] = $value['option_meta']['url'];
					}
				}
			}

			if(!empty($brand))
			{
				$_poll_data['brand'] = $brand;
				unset($_poll_data['options']['brand']);
			}
		}

		if($_options['get_tags'])
		{
			$tag = \lib\db\terms::usage($poll_id, [], 'tag', 'sarshomar%');
			$new_tag = [];

			if($tag && is_array($tag))
			{
				foreach ($tag as $key => $value)
				{
					if(isset($value['term_title']) && isset($value['id']))
					{
						$code = shortURL::encode($value['id']);
						$new_tag[$code] = $value['term_title'];
					}
				}
			}
			$_poll_data['tags'] = $new_tag;
		}

		$short_url = $host. '/$'. $_poll_data['id'];
		$_poll_data['short_url'] = $short_url;

		ksort($_poll_data);
		if(is_array($_poll_data))
		{
			// $_poll_data = array_filter($_poll_data);
		}
		// var_dump($_poll_data); exit();
		return $_poll_data;
	}
}
?>