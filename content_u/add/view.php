<?php
namespace content_u\add;

class view extends \content_u\home\view
{
	function view_filter(){}
	function view_publish(){}

	function config()
	{
		parent::config();

		// add all template of question into new file
		$this->data->template['add']['layout'] = 'content_u/add/layout.html';
		$this->data->template['add']['tree']   = 'content_u/add/tree.html';

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

	}


	/**
	 *  load data for edit
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_edit($_args)
	{
		$poll_id = $_args->api_callback;
		$poll    = \lib\db\polls::get_poll($poll_id);
		if(isset($poll['type']))
		{
			$poll['type'] = \content_u\add\model\config::set_html_type($poll['type']);
		}

		$this->data->poll = $poll;
		$answers = \lib\utility\answers::get($poll_id);
		$this->data->answers = $answers;

		$this->page_progress_url($poll_id, "add");

		if(isset($poll['parent']) && $poll['parent'] !== null)
		{
			$poll_tree = \lib\utility\poll_tree::get($poll['id']);

			if($poll_tree && is_array($poll_tree))
			{
				$opt = array_column($poll_tree, 'value');
				$this->data->poll_tree_opt = is_array($opt) ? join($opt, ',') : null;
				$this->data->poll_tree_id = \lib\utility\shortURL::encode($poll['parent']);
				$this->data->poll_tree_title = \lib\db\polls::get_poll_title($poll['parent']);
			}
		}
	}


	/**
	 * ready to load add poll
	 */
	function view_add()
	{
		$this->page_progress_url(null, "add");
	}


	/**
	 * ready to load survey mode
	 */
	function view_survey($_args)
	{
		// enable survey mod to load buttom and something else
		$this->data->survey_mod = true;
		// get survery id from url
		$survey_id = $this->model()->check_poll_url($_args);
		// get list of poll in this survey
		$poll_list = \lib\utility\survey::get_poll_list($survey_id);
		$this->data->poll_list = $poll_list;
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
			$this->data->poll_list = $_args->api_callback;
		}
	}
}
?>