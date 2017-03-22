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

				if($value === '1')
				{
					if($key === 'multi')
					{
						if(!isset($show_options['multi']))
						{
							$show_options['multi'] = ['min' => null, 'max' => null];
						}
					}
					else
					{
						$show_options[$key] = true;
					}
				}

				if($key === 'multi_min' || $key === 'multi_max')
				{
					if(!isset($show_options['multi']))
					{
						$show_options['multi'] = ['min' => null, 'max' => null];
					}
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

		if(isset($_poll_data['options']['multi']))
		{
			$_poll_data['options']['hint'] = self::set_multi_msg($_poll_data['options']['multi']);

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
				$attachment                 = \lib\db\polls::get_poll($value['option_value']);
				$attachment                 = self::get_file_url(['attachment' => $attachment]);
				$_poll_data['file']['type'] = $attachment['type'];
				$_poll_data['file']['url']  = $attachment['url'];
			}

			if(!isset($_poll_data['file']['url']))
			{
				unset($_poll_data['file']);
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


	/**
	 * make host string
	 *
	 * @param      string  $_type  The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function host($_type = null)
	{
		$host = Protocol."://" . \lib\router::get_root_domain();
		$lang = \lib\define::get_current_language_string();

		switch ($_type)
		{
			case 'file':
				return Protocol."://dl." . \lib\router::get_root_domain();
				break;

			case 'without_language':
				return $host;
				break;

			case 'with_language':
				return $host . $lang;
				break;

			default:
				return $host;
				break;
		}

	}


	/**
	 * make awaiting file url
	 *
	 * @param      <type>  $_file_type  The file type
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public static function get_file_url($_args)
	{
		$file_url      = '/static/images/awaiting.png';
		$real_file_url = '/static/images/awaiting.png';
		$file_type     = null;
		$file_mime     = null;
		$file_status   = 'draft';

		if(isset($_args['attachment']))
		{
			if(
				isset($_args['attachment']['meta']) &&
				is_string($_args['attachment']['meta']) &&
				substr($_args['attachment']['meta'], 0, 1) == '{'
			  )
			{
				$_args['attachment']['meta'] = json_decode($_args['attachment']['meta'], true);
			}

			if(isset($_args['attachment']['meta']['url']))
			{
				$real_file_url = $file_url = $_args['attachment']['meta']['url'];
			}

			if(isset($_args['attachment']['meta']['type']))
			{
				$file_type = $_args['attachment']['meta']['type'];
			}

			if(isset($_args['attachment']['meta']['mime']))
			{
				$file_mime = $_args['attachment']['meta']['mime'];
			}


			if(isset($_args['attachment']['status']))
			{
				$file_status = $_args['attachment']['status'];
			}

			if(self::$_options['load_from_site'] === true)
			{
				switch ($file_status)
				{
					case 'publish':
						$file_url = self::host('file'). '/'. $file_url;
						// no problem to load file
						break;
					case 'awaiting':
					case 'draft':
						$file_url = self::host('file'). '/static/images/awaiting.png';
						break;
					default:
						$file_url = self::host('file'). '/static/images/block.png';
						break;
				}
			}
			else
			{
				switch ($file_status)
				{
					case 'draft':
					case 'publish':
					case 'awaiting':
						$file_url = self::host('file'). '/'. $file_url;
						// no problem to load file
						break;
					default:
						$file_url = self::host('file'). '/static/images/block.png';
						break;
				}
			}

			if(self::$_options['run_options'] === false)
			{
				$file_url = self::host('file'). '/'. $real_file_url;
			}
		}
		else
		{
			$file_url = self::host('file'). $file_url;
		}

		return ['url' => $file_url, 'type' => $file_type, 'mime' => $file_mime];
	}


	/**
	 * Sets the multi message.
	 *
	 * @param      <type>  $_multi  The multi
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	private static function set_multi_msg($_multi = null)
	{
		$multi_msg = '';
		if($_multi)
		{
			$multi_min = null;
			$multi_max = null;
			// if isset min and max
			if(isset($_multi['min']))
			{
				// $multi_min = \lib\utility\human::number($_multi['min'], $this->data->site['currentlang']);
				$multi_min = \lib\utility\human::number($_multi['min']);
			}
			if(isset($_multi['max']))
			{
				$multi_max = \lib\utility\human::number($_multi['max']);
			}

			// show best message depending on min and max
			if($multi_min && $multi_max)
			{
				if($multi_min === $multi_max)
				{
					$multi_msg = T_("You must select :min options", ["min" => $multi_min]);
				}
				else
				{
					$multi_msg = T_("You can select at least :min and at most :max options", ["min" => $multi_min, "max" => $multi_max ]);
				}
			}
			elseif($multi_min)
			{
				$multi_msg = T_("You can select at least :min options", ["min" => $multi_min ]);
			}
			elseif($multi_max)
			{
				$multi_msg = T_("You can select at most :max options", ["max" => $multi_max]);
			}
			else
			{
				$multi_msg = T_("You can select all of the options");
			}
		}
		return $multi_msg;
	}

}
?>