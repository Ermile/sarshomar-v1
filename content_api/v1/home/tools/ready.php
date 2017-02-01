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
			'get_filter'         => false,
			'get_opts'           => false,
			'get_options' 		 => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'run_options' 		 => true,
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

		if(array_key_exists('status', $_poll_data))
		{
			$msg = null;
			$permission_load_poll = false;
			switch ($_poll_data['status'])
			{
				case 'publish':
					$permission_load_poll = true;
					break;
				case 'filtered':
				case 'deleted':
					$msg = T_("(The poll is :status)", ['status' => $_poll_data['status']]);
					$permission_load_poll = false;
					break;
				default:
					if(array_key_exists('user_id', $_poll_data))
					{
						if($this->user_id == $_poll_data['user_id'])
						{
							$permission_load_poll = true;
						}
						else
						{
							$msg = T_("(This is not your poll)");
						}
					}
					break;
			}

			if(!$permission_load_poll)
			{
				return debug::error(T_("Can not access to load this poll :msg",['msg' => $msg]), "id", 'permission');
			}
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

		if(array_key_exists('title', $_poll_data) && $_poll_data['title'] === '~')
		{
			$_poll_data['title'] = '';
		}

		if(array_key_exists('slug', $_poll_data) && $_poll_data['slug'] === '~')
		{
			$_poll_data['slug'] = '';
		}

		if(array_key_exists('url', $_poll_data) && $_poll_data['url'] === '~')
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
			if($_poll_data['have_score'] === '1')
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
			if($_poll_data['is_answered'] === '1')
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
			if($_poll_data['my_like'] === '1')
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
			if($_poll_data['my_fav'] === '1')
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
			if($_poll_data['have_true_answer'] === '1')
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

		// check parent and load tree data
		if(array_key_exists('parent', $_poll_data) && $_poll_data['parent'] !== null && $poll_id)
		{
			$_poll_data['tree'] = [];
			$_poll_data_tree = utility\poll_tree::get($poll_id);

			if($_poll_data_tree && is_array($_poll_data_tree))
			{
				$opt = array_column($_poll_data_tree, 'value');
				$_poll_data['tree']['answers']   = is_array($opt) ? $opt : [$opt];
				$_poll_data['tree']['parent_id'] = shortURL::encode($_poll_data['parent']);
				$_poll_data['tree']['title']     = \lib\db\polls::get_poll_title($_poll_data['parent']);
			}
		}

		if(isset($_poll_data['meta']['summary']))
		{
			$_poll_data['summary'] = $_poll_data['meta']['summary'];
			unset($_poll_data['meta']['summary']);
		}

		unset($_poll_data['meta']);

		// get opts of poll
		if($_options['get_opts'] && $poll_id)
		{
			$custom_field =
			[
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

			$answers = \lib\db\pollopts::get($poll_id, $custom_field);

			foreach ($answers as $key => $value)
			{
				if(isset($value['true']) && $value['true'] == '1')
				{
					$answers[$key]['true'] = true;
				}
				else
				{
					$answers[$key]['true'] = false;
				}
				$answers[$key] = array_filter($answers[$key]);
			}
			$_poll_data['answers'] = $answers	;
		}

		// get filters of poll
		if($_options['get_filter'] && $poll_id)
		{
			$filters               = utility\postfilters::get_filter($poll_id);
			$filters['member']     = \lib\db\ranks::get($poll_id, 'member');
			$filters               = array_filter($filters);
			$_poll_data['filters'] = $filters;
		}

		if($_options['get_public_result'] && $poll_id)
		{
			$public_result = utility\stat_polls::get_telegram_result($poll_id, true);
			$_poll_data['stats'] = $public_result;
		}


		$post_meta = \lib\db\posts::get_post_meta($poll_id);

		if(is_array($post_meta))
		{

			if($_options['get_options'])
			{
				$_poll_data['options'] = array_column($post_meta, 'option_value', 'option_key');
				foreach ($_poll_data['options'] as $key => $value)
				{
					if($value == '1')
					{
						$_poll_data['options'][$key] = true;
					}

					if($key === 'multi_min' || $key === 'multi_max')
					{
						$_poll_data['options']['multi'][substr($key,6)] = (int) $value;
						unset($_poll_data['options'][$key]);
					}
				}
			}

			$post_meta_key = array_column($post_meta, 'option_value');

			if(in_array('random_sort', $post_meta_key) && $_options['run_options'])
			{
				if(array_key_exists('answers', $_poll_data) && is_array($_poll_data['answers']))
				{
					$new  = $_poll_data['answers'];
					$keys = array_keys($_poll_data['answers']);

			        shuffle($keys);
			        foreach($keys as $key)
			        {
			        	$new[$key] = $_poll_data['answers'][$key];
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

		ksort($_poll_data);
		if(is_array($_poll_data))
		{
			// $_poll_data = array_filter($_poll_data);
		}
		// var_dump($_poll_data);
		// exit();
		return $_poll_data;
	}
}
?>