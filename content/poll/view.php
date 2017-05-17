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
			"reliable"   => T_("reliable"),
			"unreliable" => T_("unreliable"),
			"vote"       => T_("vote"),
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
			if(isset($poll['options']['hint']))
			{
				$this->data->multi_msg = $poll['options']['hint'];
			}

		}
		elseif(isset($poll['options']['ordering']))
		{
			$poll_type = 'ordering';
		}

		$this->data->poll_type = $poll_type;

		if(isset($poll['id']))
		{
			$answer_access = $this->model()->answer_lock($poll['id']);

			if(isset($answer_access['my_answer'][0]['key']) && $answer_access['my_answer'][0]['key'] === 0)
			{
				$this->data->is_skipped = true;
			}

			if(isset($answer_access['available']) && empty($answer_access['available']))
			{
				$this->data->answer_lock = true;
			}
			elseif(isset($answer_access['available']))
			{
				$this->data->answer_available = $answer_access['available'];
			}
		}


		// show simple chart on default
		$this->data->chart['stacked'] =
		[
			'result'           => T_('glance'),
		];
		// if loggined to system show more charts
		if($this->login())
		{
			$this->data->chart['stacked'] =
			[
				// 'result'           => T_('a glance'),
				'result'           => T_('glance'),
				// 'age'           => T_('age'),
				'range'            => T_('age range'),
				'gender'           => T_('gender'),
				'marrital'         => T_('marital status'),
				'graduation'       => T_('graduation'),
				// 'employmentstatus' => T_('employement status'),
				// 'province'         => T_('province'),
			];
		}


		// if language isset and its different with site language
		if(isset($poll['language']) && $poll['language'] !== $this->data->site['currentlang'])
		{
			switch ($poll['language'])
			{
				case 'fa':
					$this->data->myPollDirecton = " rtl";
					break;

				case 'en':
				default:
					$this->data->myPollDirecton = " ltr";
					break;
			}
		}

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

		if(isset($poll['result']['answers']) && is_array($poll['result']['answers']))
		{
			// get opts and fix order of them
			$descOpts = array_column($poll['result']['answers'], 'value', 'title');
			arsort($descOpts);
			$desOptscText = '';
			foreach ($descOpts as $key => $value)
			{
				if(is_numeric($value))
				{
					$desOptscText .= $key. '('. \lib\utility\human::Number($value). ') ';
				}
			}
			$this->data->page['desc'] = ''. T_('Result'). ': '. $desOptscText;

		}

		// fix link for twitter
		if(isset($poll['file']['url']) && isset($poll['file']['type']))
		{
			switch (isset($poll['file']['type']))
			{
				case 'image':
					$this->data->share['twitterCard'] = 'summary_large_image';
					$this->data->share['image']       = $poll['file']['url'];
					break;

				case 'audio':
				case 'video':
					$this->data->share['twitterCard'] = 'player';
					$this->data->share['image']       = $poll['file']['url'];
					break;

				default:
					break;
			}
		}

		$this->data->poll      = $poll;

		if(isset($poll['result']['answers']))
		{
			$myAnswerData = $poll['result']['answers'];
			// create table of results
			$this->data->poll_table = $myAnswerData;
			$this->data->poll_table = array_column($myAnswerData, 'value', 'title');
			arsort($this->data->poll_table);
			// calc total
			$total = array_sum($this->data->poll_table);
			foreach ($this->data->poll_table as $key => $value)
			{
				if(!$total)
				{
					$total = 1;
				}

				$ratio = round(($value * 100) / $total, 1);
				$this->data->poll_table[$key] =
				[
					'title' => $key,
					'value' => $value,
					'ratio' => $ratio,
				];
			}

			$this->data->poll_total_stats = json_encode($myAnswerData, JSON_UNESCAPED_UNICODE);
		}


		if($this->access('admin:admin'))
		{
			if(isset($poll['id']))
			{
				$poll_id = \lib\utility\shortURL::decode($poll['id']);
				$where =
				[
					'post_id'       => $poll_id,
					'option_cat'    => 'homepage',
					'option_key'    => 'chart',
					'option_value'  => $poll_id,
					'option_status' => 'enable',
					'limit'         => 1,
				];

				$homepage       = \lib\db\options::get($where);
				if(!empty($homepage))
				{
					$this->data->show_in_homepage = true;
				}
				$poll_ranks = \lib\db\ranks::get($poll_id);
				$this->data->poll_ranks = $poll_ranks;
			}
		}

		if(isset($poll['id']))
		{
			$this->data->status_avalible = $this->model()->status_avalible($poll['id']);
		}

		if(isset($poll['filters']))
		{
			$temp = $poll['filters'];
			unset($temp['count']);
			$this->data->poll_have_filters = (count($temp)) ? true : false;
		}
		// if(\lib\utility::get('lottery'))
		// {
		// 	if(isset($poll['id']))
		// 	{
		// 		$poll_id = \lib\utility\shortURL::decode($poll['id']);
		// 		if($poll_id)
		// 		{
		// 			$lottery = \lib\utility\lottery::run(['poll_id' => $poll_id, 'type' => \lib\utility::get('lottery')]);
		// 			var_dump($lottery);
		// 			exit();
		// 		}
		// 	}

		// }

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