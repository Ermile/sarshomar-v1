<?php
namespace content_u\add\model;
use \lib\utility;
use \lib\debug;

trait insert_poll
{

	/**
	 * insert poll
	 * get data from utility::post()
	 *
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function insert_poll($_options = [])
	{
		// get poll_type
		// the poll type in html code is defrent by the db poll type
		// this function change the html poll type to db poll type
		$poll_type = self::set_db_type(utility::post("poll_type"));
		// swich html name and db name of poll type
		if(!$poll_type)
		{
			debug::error(T_("poll type not found"));
			return false;
		}
		// default gender of all post record in sarshomar is 'poll'
		$gender = "poll";

		// get poll type from function args
		if(isset($_options['poll_type']))
		{
			$poll_type = $_options['poll_type'];
			if(substr($poll_type, 0, 6) == "survey")
			{
				// if first 6 character of poll type is 'suervey' gender of poll is 'survey'
				$gender = "survey";
			}
		}

		// check the suevey id to set in post_parent
		if(isset($_options['survey_id']) && $_options['survey_id'])
		{
			$survey_id = $_options['survey_id'];
		}
		else
		{
			$survey_id = null;
		}
		// get title
		$title   = utility::post("title");
		// get content
		$content = utility::post("description");
		// get summary of poll
		$summary = utility::post("summary");

		// check title
		if($title == null)
		{
			debug::error(T_("poll title can not null"), 'title');
			return false;
		}
		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			$summary = substr($summary, 0, 149);
		}

		$publish = 'draft';
		// save and check words
		if(!\lib\db\words::save_and_check(utility::post()))
		{
			$publish = 'awaiting';
			\lib\debug::warn(T_("You have to use words that are not approved in the text, Your text comes into review mode"));
			\lib\debug::msg('spam', \lib\db\words::$spam);
		}

		// ready to inset poll
		$args =
		[
			'user_id'        => $this->login('id'),
			'post_title'     => $title,
			'post_type'      => $poll_type,
			'post_content'   => $content,
			'post_survey'    => $survey_id,
			'post_gender'    => $gender,
			'post_privacy'   => 'public',
			'post_comment'   => 'open',
			'post_status'    => $publish,
			'post_meta'      => "{\"desc\":\"$summary\"}",
			'post_sarshomar' => $this->post_sarshomar
		];
		// inset poll if we not in update mode
		if(!$this->update_mode)
		{
			$poll_id = \lib\db\polls::insert($args);
		}
		else
		{
			// in update mode we update the poll
			$poll_id = $this->poll_id;
			\lib\db\polls::update($args, $this->poll_id);
		}

		// get the answers in $_POST
		$answers_data = $this->answers_in_post();

		$answers      = $answers_data['answers'];
		$answer_type  = $answers_data['answer_type'];
		$answer_true  = $answers_data['answer_true'];
		$answer_score = $answers_data['answer_score'];
		$answer_desc  = $answers_data['answer_desc'];

		// if in update mode first remoce the answers
		// then set the new answers again
		if($this->update_mode)
		{
			$this->remove_answers();
		}

		// the support meta
		// for every poll type we have a list of meta
		// in some mode we needless to answers
		$support_meta = self::meta($poll_type);

		if(in_array('answer', $support_meta))
		{
			// check answers
			if($answers)
			{
				// remove empty index from answer array
				$answers = array_filter($answers);
				// check the count of answer array
				if(count($answers) < 2)
				{
					debug::error(T_("you must set two answer"), ['answer1']);
					return false;
				}
				// combine answer type and answer text and answer score
				$combine = [];
				foreach ($answers as $key => $value)
				{
					$combine[] =
					[
						'true'  => isset($answer_true[$key])  ? $answer_true[$key] 	: null,
						'score' => isset($answer_score[$key]) ? $answer_score[$key] : null,
						'type'  => isset($answer_type[$key])  ? $answer_type[$key] 	: null,
						'desc'  => isset($answer_desc[$key])  ? $answer_desc[$key] 	: null,
						'txt'   => $value
		     		];
				}
				$answers_arg =
				[
					'poll_id' => $poll_id,
					'answers' => $combine
				];
				$answers = \lib\utility\answers::insert($answers_arg);
			}
			else
			{
				debug::error(T_("answers not found"), ['answer1', 'answer2']);
				return false;
			}
		}

		// in update mode we first remove all meta of the poll
		// and then insert the meta again
		if($this->update_mode)
		{
			$this->remove_meta();
		}

		// remve the key of 'answer' suppot meta
		if(($key = array_search('answer', $support_meta)) !== false)
		{
		    unset($support_meta[$key]);
		}

		$insert_meta = [];
		$post_meta   = [];

		foreach ($support_meta as $key => $value)
		{
			// check the meta isset and !is_null()
			if(utility::post("meta_$value") != '')
			{
				// save the lock of this poll and profile item
				$profile_lock = null;
				if($value == "profile")
				{
					if(utility::post("meta_profile") != '' && $this->access('u', 'complete_profile', 'admin'))
					{
						$profile_lock = utility::post("meta_profile");
					}
					else
					{
						continue;
					}
				}

				// check permission of hidden result
				if($value == "hidden_result" && !$this->access('u', 'hidden_result', 'admin'))
				{
					continue;
				}

				$insert_meta[] =
				[
					'post_id'      => $poll_id,
					'option_cat'   => "poll_$poll_id",
					'option_key'   => 'meta',
					'option_value' => $value,
					'option_meta'  => $profile_lock
				];

				// save the meta in post_meta fields
				$post_meta[$value] = utility::post("meta_$value");
				// comment met must be save in post_comment fields
				if($value == 'comment')
				{
					\lib\db\polls::update(['post_comment' => 'open'], $poll_id);
				}
			}
		}

		if(!empty($insert_meta))
		{
			// insert the meta in options table
			\lib\db\options::insert_multi($insert_meta);
			// save meta in post_meta fields
			\lib\db\polls::merge_meta($post_meta, $poll_id);
		}

		if(\lib\debug::$status)
		{
			\lib\utility\profiles::set_dashboard_data($this->login('id'), 'my_poll');
			\lib\debug::true(T_("add poll Success"));
			return $poll_id;
		}
		else
		{
			\lib\debug::error(T_("Error in add poll"));
			return false;
		}
	}
}
?>