<?php
namespace content_u\add;

class view extends \content_u\home\view
{
	use filter\view;
	use publish\view;

	function config()
	{

		parent::config();

		// add all template of question into new file
		$this->data->template['add']['layout'] = 'content_u/add/layout.html';
		$this->data->template['add']['tree']   = 'content_u/add/tree.html';

		$this->data->step =
		[
			'current'      => 'add',
			'add'          => true,
			'filter'       => false,
			'publish'      => false,
			'link_add'     => false,
			'link_filter'  => false,
			'link_publish' => false,
		];
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

		$url = $this->url('baseLang'). 'add/'. isset($poll['poll']['id']) ? $poll['poll']['id'] : null;

		$this->data->step =
		[
			'current'      => 'add',
			'add'          => true,
			'filter'       => false,
			'publish'      => false,
			'link_add'     => $url,
			'link_filter'  => $url. '/filter',
			'link_publish' => $url. '/publish',
		];
		
		$this->data->poll    = isset($poll['poll']) 	? $poll['poll'] 	: null;
		$this->data->answers = isset($poll['answers']) 	? $poll['answers'] 	: null;
		
		$this->data->poll_tree_opt   = isset($poll['poll_tree_opt']) 	? $poll['poll_tree_opt'] 	: null;
		$this->data->poll_tree_id    = isset($poll['poll_tree_id']) 	? $poll['poll_tree_id'] 	: null;
		$this->data->poll_tree_title = isset($poll['poll_tree_title']) 	? $poll['poll_tree_title'] 	: null;
	
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
			$reg = "/@\/add\/([". $this->controller()::$shortURL ."]+)$/";
			if(preg_match($reg, $_SERVER['HTTP_REFERER'], $split))
			{
				if(isset($split[1]))
				{
					$current_poll_id = $split[1]; // the current poll id
					$current_poll_id = \lib\utility\shortURL::decode($current_poll_id);

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