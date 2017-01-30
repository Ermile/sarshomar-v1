<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait ready
{
	public $shortURL = shortURL::ALPHABET;

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
	public function ready_poll($_poll_data, $_options = [])
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

		$poll_id = 0;

		// encode id
		if(isset($_poll_data['id']))
		{
			$poll_id          = $_poll_data['id'];
			$_poll_data['id'] = shortURL::encode($_poll_data['id']);
		}

		// check id
		if(!$poll_id)
		{
			return debug::error(T_("Poll not found"), "id", 'arguments');
		}

		if(isset($_poll_data['status']))
		{
			$permission_load_poll = false;
			switch ($_poll_data['status'])
			{
				case 'publish':
					$permission_load_poll = true;
					break;
				case 'filtered':
					$permission_load_poll = false;
					break;
				default:
					if(isset($_poll_data['user_id']))
					{
						if($this->user_id == $_poll_data['user_id'])
						{
							$permission_load_poll = true;
						}
					}
					break;
			}

			if(!$permission_load_poll)
			{
				return debug::error(T_("Can not access to load this poll"), "id", 'permission');
			}
		}

		if(isset($_poll_data['title']) && $_poll_data['title'] === '~')
		{
			$_poll_data['title'] = '';
		}


		if(isset($_poll_data['slug']) && $_poll_data['slug'] === '~')
		{
			$_poll_data['slug'] = '';
		}


		if(isset($_poll_data['url']) && $_poll_data['url'] === '~')
		{
			$_poll_data['url'] = '';
		}

		// encode user id
		if(isset($_poll_data['user_id']))
		{
			$_poll_data['user_id'] = shortURL::encode($_poll_data['user_id']);
		}

		// encode parent
		if(isset($_poll_data['parent']))
		{
			$_poll_data['parent'] = shortURL::encode($_poll_data['parent']);
		}

		if(isset($_poll_data['content']))
		{
			$_poll_data['description'] = $_poll_data['content'];
		}

		// encode suervey
		if(isset($_poll_data['survey']))
		{
			$_poll_data['survey'] = shortURL::encode($_poll_data['survey']);
		}

		// change have_score field
		if(isset($_poll_data['have_score']))
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

		// change sarshomar field
		if(isset($_poll_data['sarshomar']) && $_poll_data['sarshomar'])
		{
			$_poll_data['sarshomar'] = true;
		}

		// change is_answered parametr
		if(isset($_poll_data['is_answered']) && $_poll_data['is_answered'])
		{
			$_poll_data['is_answered'] = true;
		}

		// change my_fav parametr
		if(isset($_poll_data['my_fav']) && $_poll_data['my_fav'])
		{
			$_poll_data['my_fav'] = true;
		}

		// change my_like parametr
		if(isset($_poll_data['my_like']) && $_poll_data['my_like'])
		{
			$_poll_data['my_like'] = true;
		}

		// check parent and load tree data
		if(isset($_poll_data['parent']) && $_poll_data['parent'] !== null && $poll_id)
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
			}

			$post_meta_key = array_column($post_meta, 'option_value');

			if(in_array('random_sort', $post_meta_key) && $_options['run_options'])
			{
				if(isset($_poll_data['answers']) && is_array($_poll_data['answers']))
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

		if(is_array($_poll_data))
		{
			$_poll_data = array_filter($_poll_data);
		}
		// var_dump($_poll_data);
		// exit();
		return $_poll_data;
	}
}
?>