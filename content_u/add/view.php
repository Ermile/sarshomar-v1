<?php
namespace content_u\add;
use \lib\utility;
use \lib\utility\shortURL;

class view extends \content_u\home\view
{
	use filter\view;
	use publish\view;

	function config()
	{

		parent::config();

		// add all template of question into new file
		$this->data->template['add']['layout'] = 'content_u/add/layout.html';
		// $this->data->template['add']['tree']   = 'content_u/add/tree.html';

		$this->data->page['title']   = T_('Add');
		$this->data->page['desc']    = T_("Add new poll");


		// check permisson
		if($this->access('u', 'complete_profile', 'admin'))
		{
			$profile_lock = \lib\db\filters::support_filter();
			$profile = [];
			foreach ($profile_lock as $key => $value)
			{
				if(is_array($value))
				{
					$trans = [];
					foreach ($value as $k => $v)
					{
						$trans[] = T_($v);
					}
					$value = join($trans, ",");
				}
				$profile[$key] = $value;
			}
			$this->data->profile_lock = $profile;
		}

		// load empty answers for first loading
		$this->data->answers = [[],[]];
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
			$cat_level = [];

			while (array_key_exists('term_parent', $cat))
			{
				if(isset($cat['term_title']))
				{
					array_push($cat_level, $cat['term_title']);
				}
				if($cat['term_parent'])
				{
					$cat = \lib\db\terms::get($cat['term_parent']);
				}
				else
				{
					$cat = [];
				}
			}
			$this->data->cats = array_reverse($cat_level);
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
		// exit();
		// $this->data->poll_tree_opt   = isset($poll['poll_tree_opt']) 	? $poll['poll_tree_opt'] 	: null;
		// $this->data->poll_tree_id    = isset($poll['poll_tree_id']) 	? $poll['poll_tree_id'] 	: null;
		// $this->data->poll_tree_title = isset($poll['poll_tree_title']) 	? $poll['poll_tree_title'] 	: null;
		// $this->data->poll_id         = '/'. $poll_id;
	}


	/**
	 * show search result
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_tree($_args)
	{
		// only show with referrer
		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '@/add') !== false)
		{
			$reg = "/@\/add\/([". $this->controller()::$shortURL ."]+)$/";
			if(preg_match($reg, $_SERVER['HTTP_REFERER'], $split))
			{
				if(isset($split[1]))
				{
					$current_poll_id = $split[1]; // the current poll id
					$current_poll_id = shortURL::decode($current_poll_id);

					// get this poll to find poll_parent
					$poll = \lib\db\polls::get_poll($current_poll_id);
					if(isset($poll['parent']))
					{
						// the parent poll id must be checked
						$parent = $poll['parent'];
						$this->data->parent_poll_id = $parent;

						// get the poll tree to find the opt lock on this poll
						$poll_tree = \lib\utility\poll_tree::get($current_poll_id);
						if($poll_tree && is_array($poll_tree))
						{
							$opt = array_column($poll_tree, 'value');
							$this->data->parent_poll_opt = $opt;
						}
					}
				}
			}
			$this->data->poll_list = $_args->api_callback;
		}
	}
}
?>