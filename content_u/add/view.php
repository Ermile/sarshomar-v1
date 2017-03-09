<?php
namespace content_u\add;
use \lib\utility;
use \lib\utility\shortURL;

class view extends \content_u\home\view
{
	/**
	 * config
	 */
	function config()
	{

		parent::config();

		// add all template of question into new file
		$this->data->template['add']['layout'] = 'content_u/add/layout.html';
		// $this->data->template['add']['tree']   = 'content_u/add/tree.html';

		$this->data->page['title']   = T_('Add');
		$this->data->page['desc']    = T_("Add new poll");

		// load empty answers for first loading
		$this->data->answers = [[],[]];
		// $this->data->member_exist = \lib\db\users::get_count('valid');


		// set person selector range values
		$this->set_range_default_persons();
		// set multiple default values
		$this->set_range_default_multiple();

	}

	/**
	 *  load data for edit
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_edit($_args)
	{

		$poll = $_args->api_callback;
		if(isset($poll['answers']))
		{
			if(count($poll['answers']) >= 1)
			{
				$answers = $poll['answers'];
				array_push($answers, []);
			}
			else
			{
				$answers = [[],[]];
			}
		}
		else
		{
			$answers = [[],[]];
		}

		$this->data->answers = $answers;

		$answer_type = array_column($answers, 'type');

		if(in_array("descriptive", $answer_type))
		{
			$this->data->have_other_opt = true;
		}

		unset($poll['answers']);

		$this->data->edit_mode = true;

		if(isset($poll['tree']['parent']))
		{
			$this->data->poll_parent_opts = \lib\db\pollopts::get(shortURL::decode($poll['tree']['parent']), ['key', 'title']);
		}

		if(isset($poll['options']['cat']) && isset($poll['id']))
		{
			$cat_id    = shortURL::decode($poll['options']['cat']);
			$cat       = \lib\db\terms::get($cat_id);

			if(isset($cat['term_meta']))
			{
				$meta = $cat['term_meta'];
				if(is_string($cat['term_meta']) && substr($cat['term_meta'], 0, 1) === '{')
				{
					$meta = json_decode($cat['term_meta'], true);
				}

				$language = \lib\define::get_language();
				if(isset($meta['translate'][$language]))
				{
					$this->data->cat_title = $meta['translate'][$language];
				}
			}

			if(isset($cat['term_title']))
			{
				if($cat['term_title'] != T_($cat['term_title']))
				{
					$this->data->cats = [$cat['term_title'] . " | ". T_($cat['term_title'])];
				}
				else
				{
					$this->data->cats = [$cat['term_title']];
				}
			}

			// $cat_level = [];

			// while (array_key_exists('term_parent', $cat))
			// {
			// 	if(isset($cat['term_title']))
			// 	{
			// 		array_push($cat_level, $cat['term_title']);
			// 	}
			// 	if($cat['term_parent'])
			// 	{
			// 		$cat = \lib\db\terms::get($cat['term_parent']);
			// 	}
			// 	else
			// 	{
			// 		$cat = [];
			// 	}
			// }
			// $this->data->cats = array_reverse($cat_level);
		}
		if(isset($poll['articles']) && is_array($poll['articles']) && !empty($poll['articles']))
		{
			$article_titles = [];
			foreach ($poll['articles'] as $key => $value)
			{
				$id = shortURL::decode($value);
				if($id)
				{
					$article_titles[$value] =  \lib\db\polls::get_poll_title($id);
				}
			}
			$this->data->article_titles = $article_titles;
		}

		if(isset($poll['status']))
		{
			if($poll['status'] != 'draft' && $this->access('admin'))
			{
				$this->data->real_status = $poll['status'];
				$poll['status'] = 'draft';
			}
		}

		if(isset($poll['filters']['count']))
		{
			$this->set_range_default_persons($poll['filters']);
		}

		if(isset($poll['tags']) && is_array($poll['tags']))
		{
			$this->data->poll_tags_code = json_encode(array_column($poll['tags'],'title'), JSON_UNESCAPED_UNICODE);
		}

		// set multiple default values
		$this->set_range_default_multiple(count($answers) - 1);


		$this->data->poll = $poll;
	}


	/**
	 * [set_range_default_persons description]
	 * @param [type] $_filter [description]
	 */
	function set_range_default_persons($_filter = null)
	{
		if($_filter)
		{
			// set limit of select in this condition
			$filters = $_filter;
			unset($filters['count']);
			$limit_of_filters = (int) \lib\db\filters::count_user($filters);
			if($limit_of_filters)
			{
				$this->data->persons['limit'] = $limit_of_filters;
			}

			// if he is sarshomar user and previously set max value, use max value for it now
			if($this->access('u', 'sarshomar', 'view') && (int) $_filter['count'] === 1000000000)
			{
				$this->data->persons['from'] = $this->data->persons['max'];
			}
			// else if user set value for persons, set it as default
			else if(isset($_filter['count']))
			{
				$this->data->persons['from'] = $_filter['count'];
			}
			else
			{
				$this->data->persons['from'] = 15;
			}
			return true;
		}
		// define variable to set for persons variable
		$persons = [];
		// set maximum user allowed
		$persons['max'] = \lib\db\users::get_count("all");
		if(!$persons['max'])
		{
			$persons['max'] = 0;
		}
		// change step of change handler
		$persons['step'] = round($persons['max'] / 1000);
		if($persons['step'] < 1)
		{
			$persons['step'] = 1;
		}
		elseif($persons['step'] > 10)
		{
			$persons['step'] = 10;
		}
		// default set to zero for from value
		if($this->access('u', 'sarshomar', 'view'))
		{
			$persons['from'] = $persons['max'];
		}
		else
		{
			$persons['from'] = 0;
		}
		// set into twig variable
		$this->data->persons = $persons;
	}


	/**
	 * [set_range_default_multiple description]
	 * @param integer $_answerCount [description]
	 */
	function set_range_default_multiple($_answerCount = 2)
	{
		$multiple = [];
		if($_answerCount < 2)
		{
			$_answerCount = 2;
		}
		$multiple['max'] = $_answerCount;
		$this->data->multiple = $multiple;
	}
}
?>