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
		$this->data->member_exist = \lib\db\users::get_count('valid');
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

		$this->data->poll      = $poll;
		$this->data->edit_mode = true;

		if(isset($poll['tree']['parent']))
		{
			$this->data->poll_parent_opts = \lib\db\pollopts::get(shortURL::decode($poll['tree']['parent']), ['key', 'title']);
		}

		if(isset($poll['options']['cat']) && isset($poll['id']))
		{
			$cat_id    = shortURL::decode($poll['options']['cat']);

			$cat       = \lib\db\terms::get($cat_id);
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

		if(isset($poll['filters']['count']))
		{
			$filters = $poll['filters'];
			unset($filters['count']);
			$member_exist = (int) \lib\db\filters::count_user($filters);
			$total_with_filter = $member_exist;
			$total_users = \lib\db\users::get_count("all");
			if($this->access('u', 'sarshomar', 'view') && (int) $poll['filters']['count'] === 1000000000)
			{
				$selected_user = $total_with_filter;
			}
			else
			{
				$selected_user = $poll['filters']['count'];
			}
			$this->data->total_users         = $total_users;
			$this->data->total_with_filter   = $total_with_filter;
			$this->data->selected_user       = $selected_user;
		}
	}
}
?>