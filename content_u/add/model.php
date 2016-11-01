<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{

	/**
	 * get data to add new add
	 */
	function post_add($_args)
	{
		// start transaction
		\lib\db::transaction();

		// default survey id is null
		$survey_id = null;
		// users click on one of 'add filter' buttom
		if(utility::post("filter") || utility::post("publish"))
		{
			$insert_poll = null;
			// if title is null and answer is null
			// we check the url
			// if in the survey we abrot save poll and redirect to filter page
			// user discard the poll
			if(utility::post("title") == '' && empty(array_filter(utility::post("answers"))))
			{
				// if we not in survey we have error for title and answers
				if(!$this->check_poll_url($_args))
				{
					debug::error(T_("title or answers must be full"));
				}
			}
			else
			{
				// we not in survey mode
				if(!$this->check_poll_url($_args))
				{
					// insert the poll
					$insert_poll = $this->insert_poll();
				}
				else
				{
					// users click on this buttom and the page has a data for insert
					// we check the poll or survey mod
					// if in survey mode we need to save last poll in user page as a survey record
					// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
					$poll_type = "survey_poll_";
					// get the survey id and survey url
					$survey_id = $this->check_poll_url($_args, "decode");
					$survey_url = $this->check_poll_url($_args, "encode");
					// insert the poll
					$insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
					// save survey title
					$this->set_suervey_title($survey_id);
				}
			}
			// check the url
			if($this->check_poll_url($_args))
			{
				// url like this >> @/(.*)/add
				$url       = $this->check_poll_url($_args, "encode");
			}
			else
			{
				// the url is @/add
				$url = \lib\utility\shortURL::encode($insert_poll);
			}
			if(debug::$status)
			{
				// must be redirect to filter page
				if(utility::post("filter"))
				{
					$this->redirector()->set_url("@/add/$url/filter");
				}
				// must be redirect to publish page
				elseif(utility::post("publish"))
				{
					$this->redirector()->set_url("@/add/$url/publish");
				}
				else
				{
					debug::error(T_("can not found redirect page"));
				}
			}
		}
		elseif(utility::post("survey"))
		{
			// the user click on this buttom
			// we save the survey
			$args =
			[
				'user_id'     => $this->login('id'),
				'post_title'  => 'untitled survey',
				'post_type'   => 'survey_private',
				'post_gender' => 'survey',
				'post_status' => 'draft',
			];
			$survey_id = \lib\db\survey::insert($args);
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			$poll_type = "survey_poll_";
			// insert the poll
			$insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
			// redirect to @/$url/add to add another poll
			$url = \lib\utility\shortURL::encode($survey_id);
			if($insert_poll)
			{
				$this->redirector()->set_url("@/add/$url");

			}

		}
		elseif(utility::post("add_poll"))
		{
			//users click on this buttom
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			$poll_type = "survey_poll_";
			// get the survey id and survey url
			$survey_id = $this->check_poll_url($_args, "decode");
			$survey_url = $this->check_poll_url($_args, "encode");
			// insert the poll
			$insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
			// save survey title
			$this->set_suervey_title($survey_id);
			// redirect to '@/survey id /add' to add another poll
			if($insert_poll)
			{
				$this->redirector()->set_url("@/add/$survey_url");
			}
		}
		else
		{
			// the user click on buttom was not support us !!
			debug::error(T_("command not found"));
		}


		// save poll tree
		if(utility::post("parent_tree_id") && utility::post("parent_tree_opt"))
		{
			$arg =
			[
				'parent' => utility::post("parent_tree_id"),
				'opt'    => utility::post("parent_tree_opt"),
				'child'  => $insert_poll
			];
			$result = \lib\db\poll_tree::set($arg);
		}

		if(debug::$status)
		{
			\lib\db::commit();
		}
		else
		{
			\lib\db::rollback();
		}
	}

	/**
	 * Sets the suervey title.
	 */
	public function set_suervey_title($_survey_id)
	{
		//save survey name
		if(utility::post("survey_title"))
		{
			// polls::update_url() has retrun  '$/[shortURL of survey id ]/suervy_title'
			$args =
			[
				'post_title'  => utility::post("survey_title"),
				'post_url'    => \lib\db\polls::update_url($_survey_id, utility::post("survey_title"), false),
				'post_gender' => 'survey',
				'post_slug'   => \lib\utility\filter::slug(utility::post("survey_title"))
			];
			$result = \lib\db\survey::update($args, $_survey_id);
			if(!$result)
			{
				debug::error(T_("error in save survey title"));
			}
		}
	}

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
		$poll_type    = utility::post("poll_type");
		// swich html name and db name of poll type
		switch ($poll_type)
		{
			case 'multiple_choice':
				$poll_type = 'select';
				break;

			case 'descriptive':
				$poll_type = 'text';
				break;

			case 'notification':
				$poll_type = 'notify';
				break;

			case 'upload':
				$poll_type = 'upload';
				break;

			case 'starred':
				$poll_type = 'star';
				break;

			case 'numerical':
				$poll_type = 'number';
				break;

			case 'sort':
				$poll_type = 'order';
				break;

			// $poll_type = 'media_image';
			// $poll_type = 'media_video';
			// $poll_type = 'media_audio';

			default:
				debug::error(T_("poll type not found"));
				return false;
				break;
		}
		// default gender of all post record in sarshomar is 'poll'
		$gender = "poll";

		// get poll type from function args
		if(isset($_options['poll_type']))
		{
			$poll_type = $_options['poll_type']. $poll_type;
			if(substr($poll_type, 0, 6) == "survey")
			{
				// if first 6 character of poll type is 'suervey' gender of poll is 'survey'
				$gender = "survey";
			}
		}
		else
		{
			$poll_type = 'poll_private_'. $poll_type;
		}
		// check the suevey id to set in post_parent
		if(isset($_options['survey_id']))
		{
			$survey_id = $_options['survey_id'];
		}
		else
		{
			$survey_id = null;
		}
		// get title
		$title        = utility::post("title");
		// get content
		$content      = utility::post("description");
		// get summary of poll
		$summary      = utility::post("summary");
		// get answers
		$answers      = utility::post("answers");
		// get answers type
		$answer_type  = utility::post("answer_type");
		// get answers true
		$answer_true  = utility::post("answer_true");
		// get answers point
		$answer_point = utility::post("answer_point");
		// get answers desc
		$answer_desc  = utility::post("answer_desc");

		// check title
		if($title == null)
		{
			debug::error(T_("poll title can not null"));
			return false;
		}
		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			debug::error(T_("summary text must be less than 150 character"));
			return false;
		}
		// ready to inset poll
		$args =
		[
			'user_id'      => $this->login('id'),
			'post_title'   => $title,
			'post_type'    => $poll_type,
			'post_content' => $content,
			'post_survey'  => $survey_id,
			'post_gender'  => $gender,
			'post_status'  => 'draft',
			'post_meta'    => "{\"desc\":\"$summary\"}"
		];
		// inset poll
		$poll_id = \lib\db\polls::insert($args);
		// check answers
		if($answers)
		{
			// if answers is not array return false
			if(!is_array($answers))
			{
				debug::error(T_("answer must be array"));
				return false;
			}
			// remove empty index from answer array
			$answers = array_filter($answers);
			// check the count of answer array
			if(count($answers) < 2)
			{
				debug::error(T_("you must set two answer"));
				return false;
			}
			// combine answer type and answer text and answer point
			$combine = [];
			foreach ($answers as $key => $value)
			{
				$combine[] =
				[
					'true'  => isset($answer_true[$key])  ? $answer_true[$key] 	: null,
					'point' => isset($answer_point[$key]) ? $answer_point[$key] : null,
					'type'  => isset($answer_type[$key])  ? $answer_type[$key] 	: null,
					'txt'   => $value
	     		];
			}
			$answers_arg =
			[
				'poll_id' => $poll_id,
				'answers' => $combine
			];
			$answers = \lib\db\answers::insert($answers_arg);
		}
		else
		{
			debug::error(T_("answers not found"));
			return false;
		}
		// get the metas of this poll
		$metas = [];
		$insert_meta = false;
		foreach (utility::post() as $key => $value)
		{
			if(preg_match("/^meta\_(.*)$/", $key, $meta))
			{
				if(isset($meta[1]))
				{
					// save the lock of this poll and profile item
					$profile_lock = null;
					if($meta[1] == "profile")
					{
						if(utility::post("meta_profile") != '')
						{

							$profile_lock = utility::post("meta_profile");

						}
						else
						{
							continue;
						}
					}

					$metas[] =
					[
						'post_id'      => $poll_id,
						'option_cat'   => "poll_$poll_id",
						'option_key'   => 'meta',
						'option_value' => $meta[1],
						'option_meta'  => $profile_lock
					];
					$insert_meta = true;
				}
			}
		}
		if($insert_meta)
		{
			$save_poll_metas = \lib\db\options::insert_multi($metas);
		}
		if($answers)
		{
			\lib\debug::true(T_("add poll Success"));
			return $poll_id;
		}
		else
		{
			\lib\debug::error(T_("Error in add poll"));
			return false;
		}
	}


	/**
	 * set survey.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_survey($_args)
	{
		$poll_id = $_args->match->url[0][1];
		if($poll_id)
		{
			$poll_id = \lib\utility\shortURL::decode($poll_id);
		}
	}
}
?>