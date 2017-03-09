<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_options
{
	private static function get_options(&$_poll_data)
	{
		$post_meta = \lib\db\posts::get_post_meta(self::$private_poll_id);
		if(is_array($post_meta))
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
				if(isset($_poll_data['options']) && is_array($_poll_data['options']) && is_array($show_options))
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
				$attachment = \lib\db\polls::get_poll($value['option_value']);
				if(isset($attachment['meta']['url']))
				{
					if(self::$_options['run_options'] && isset($attachment['status']) && $attachment['status'] != 'publish')
					{
						$_poll_data['file']['url'] = $awaiting_file_url;
					}
					else
					{
						$_poll_data['file']['url'] = $host. '/'. $attachment['meta']['url'];
					}
				}

				if(isset($value['option_meta']['type']))
				{
					$_poll_data['file']['type'] = $value['option_meta']['type'];
				}
			}
		}

		$_poll_data['articles'] = $poll_articles;

		if(!empty($poll_tree_answer) && isset($_poll_data['parent']))
		{
			$_poll_data['tree'] = [];
			// $_poll_data_tree = utility\poll_tree::get(self::$private_poll_id);

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

		if(in_array('random_sort', $post_meta_key) && self::$_options['run_options'])
		{
			if(array_key_exists('answers', $_poll_data) && is_array($_poll_data['answers']))
			{
				$new  = [];
				$keys = array_keys($_poll_data['answers']);
		        shuffle($keys);

		        foreach($keys as $key => $value)
		        {
		        	$new[$value] = $_poll_data['answers'][$value];
		        }
		        $_poll_data['answers'] = $new;
			}
		}

		if(in_array('hidden_result', $post_meta_key) && self::$_options['run_options'])
		{
			unset($_poll_data['result']);
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

		$cat = \lib\db\terms::usage(self::$private_poll_id, [], 'cat', 'sarshomar');
		if($cat)
		{
			if(isset($cat[0]['id']))
			{
				$_poll_data['options']['cat'] = shortURL::encode($cat[0]['id']);
			}
		}
	}
}
?>