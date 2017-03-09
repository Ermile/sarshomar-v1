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

		$this->data->poll_trans =
		[
			"trust"   => T_("trust"),
			"untrust" => T_("unreliable"),
			"vote"    => T_("vote"),
		];
		$this->data->poll_trans = json_encode($this->data->poll_trans, JSON_UNESCAPED_UNICODE);

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
			$this->data->multi_msg = $this->set_multi_msg($poll['options']['multi']);

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


		$this->data->chart['stacked'] =
		[
			'gender'           => T_('Gender'),
			// 'age'           => T_('Age'),
			'range'            => T_('Age range'),
			'marrital'         => T_('Marrital status'),
			'graduation'       => T_('Graduation'),
			'employmentstatus' => T_('Employment status'),
			'province'         => T_('Province'),
		];

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

		if(isset($poll['result']['answers']))
		{
			$myChart = $poll['result']['answers'];
			$this->data->poll_total_stats = json_encode($myChart, JSON_UNESCAPED_UNICODE);
		}
	}


	/**
	 * set custom message for multi mode
	 * @param [type] $_multi [description]
	 */
	private function set_multi_msg($_multi = null)
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
					$multi_msg = T_("You should exactly select :min options", ["min" => $multi_min]);
				}
				else
				{
					$multi_msg = T_("You can select at least :min and at most :max options", ["min" => $multi_min, "max" => $multi_max ]);
				}
			}
			elseif($multi_min)
			{
				$multi_msg = T_("You should select at least :min options", ["min" => $multi_min ]);
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