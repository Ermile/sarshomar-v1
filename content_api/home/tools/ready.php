<?php
namespace content_api\home\tools;
use \lib\utility;
use \lib\debug;

trait ready
{
	public $shortURL = utility\shortURL::ALPHABET;

	/**
	 * ready poll record to show
	 *
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
			'get_public_result'  => false,
			'get_advance_result' => false,
		];
		$_options = array_merge($default_options, $_options);

		$poll_id = 0;

		if(isset($_poll_data['id']))
		{
			$poll_id          = $_poll_data['id'];
			$_poll_data['id'] = utility\shortURL::encode($_poll_data['id']);
		}

		if(!$poll_id)
		{
			return debug::error(T_("Poll not found"), "id", 'arguments');
		}

		if(isset($_poll_data['user_id']))
		{
			$_poll_data['user_id'] = utility\shortURL::encode($_poll_data['user_id']);
		}

		if(isset($_poll_data['parent']))
		{
			$_poll_data['parent'] = utility\shortURL::encode($_poll_data['parent']);
		}

		if(isset($_poll_data['survey']))
		{
			$_poll_data['survey'] = utility\shortURL::encode($_poll_data['survey']);
		}

		if(isset($_poll_data['sarshomar']) && $_poll_data['sarshomar'])
		{
			$_poll_data['sarshomar'] = true;
		}

		if(isset($_poll_data['is_answered']) && $_poll_data['is_answered'])
		{
			$_poll_data['is_answered'] = true;
		}

		if(isset($_poll_data['my_fav']) && $_poll_data['my_fav'])
		{
			$_poll_data['my_fav'] = true;
		}

		if(isset($_poll_data['my_like']) && $_poll_data['my_like'])
		{
			$_poll_data['my_like'] = true;
		}

		if(isset($_poll_data['parent']) && $_poll_data['parent'] !== null && $poll_id)
		{
			$_poll_data['tree'] = [];
			$_poll_data_tree = utility\poll_tree::get($poll_id);

			if($_poll_data_tree && is_array($_poll_data_tree))
			{
				$opt = array_column($_poll_data_tree, 'value');
				$_poll_data['tree']['answers']   = is_array($opt) ? $opt : [$opt];
				$_poll_data['tree']['parent_id'] = utility\shortURL::encode($_poll_data['parent']);
				$_poll_data['tree']['title']     = \lib\db\polls::get_poll_title($_poll_data['parent']);
			}
		}

		if($_options['get_opts'] && $poll_id)
		{
			$answers = \lib\db\pollopts::get($poll_id);
			$_poll_data['answers'] = $answers;
		}

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
			$_poll_data['result'] = $public_result;
		}


		$post_meta = \lib\db\posts::get_post_meta($poll_id);
		if(is_array($post_meta))
		{
			$post_meta_key = array_column($post_meta, 'option_value');

			if(in_array('random_sort', $post_meta_key))
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
			}

			if(in_array('hidden_result', $post_meta_key))
			{
				unset($_poll_data['result']);
			}

		}

		if(is_array($_poll_data))
		{
			$_poll_data = array_filter($_poll_data);
		}

		return $_poll_data;
	}
}
?>