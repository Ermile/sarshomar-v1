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

		$poll_type = 'select';
		if(isset($poll['options']['multi']))
		{
			$poll_type = 'multi';
		}
		elseif(isset($poll['options']['ordering']))
		{
			$poll_type = 'ordering';
		}

		$this->data->poll_type = $poll_type;

		if(isset($poll['id']))
		{
			$this->data->answer_lock = $this->model()->answer_lock($poll['id']);
		}

		$advance_stats_title = [];
		if(isset($poll['stats']['advance_stats']['valid']))
		{
			$advance_stats_title = array_keys($poll['stats']['advance_stats']['valid']);
		}

		if(isset($poll['stats']['advance_stats']['invalid']))
		{
			$advance_stats_title = array_merge($advance_stats_title,array_keys($poll['stats']['advance_stats']['invalid']));
		}

		$this->data->chart['stacked'] = array_flip($advance_stats_title);

		// set title and desc of each page
		$poll_title = null;
		// set page title
		if(isset($poll['title']))
		{
			$poll_title = $poll['title'];
		}
		else
		{
			$poll_title = T_('undefined Title!');
		}
		// set sarshomar knowledge or personal
		if(isset($poll['sarshomar']))
		{
			$this->data->site['title'] = T_("Sarshomar Knowledge");
		}
		else
		{
			// set username or user nickname in future
		}
		// set page title
		$this->data->page['title'] = $poll_title;

		// set page desc
		if(isset($poll['summary']) && $poll['summary'])
		{
			$this->data->page['desc'] = $poll['summary'];
		}
		elseif(isset($poll['description']) && $poll['description'])
		{
			$this->data->page['desc'] = $poll['description'];
		}
		else
		{
			$this->data->page['desc'] = $poll_title;
		}


		if(isset($poll['is_answered']) && $poll['is_answered'] === true && isset($poll['id']) && $this->login())
		{
			$my_answer = \lib\utility\answers::is_answered($this->login('id'), \lib\utility\shortURL::decode($poll['id']));

			if($my_answer && is_array($my_answer))
			{
				$poll['my_answer'] = array_column($my_answer, 'txt', 'opt');
			}
			else
			{
				$poll['my_answer'] = [];
			}
		}
		$this->data->poll      = $poll;

		if(isset($poll['stats']['total']['valid']))
		{
			$this->data->poll_total_stats = json_encode($poll['stats']['total']['valid'], JSON_UNESCAPED_UNICODE);
		}
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