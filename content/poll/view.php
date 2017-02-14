<?php
namespace content\poll;
use \lib\utility\shortURL;

class view extends \mvc\view
{
	function config()
	{
		// add all template of poll into new file
		$this->data->template['poll']['layout'] = 'content/poll/layout.html';

		if ($this->module() === 'home')
		{
			$this->include->js_main = true;
		}

		$this->include->js          = true;
		$this->include->chart       = true;
	}


	/**
	 * show one poll to answer user
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_poll($_args)
	{
		$poll = $_args->api_callback;

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
		$this->data->poll = $poll;
	}


	/**
	 * get all comments of this poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_comments($_args)
	{
		$this->data->show_all_comments = true;
		$this->data->all_comments = $_args->api_callback;
	}
}
?>