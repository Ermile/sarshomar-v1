<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{

	/**
	 * Gets the edit.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The edit.
	 */
	public function get_edit($_args)
	{
		$poll_id = $this->check_poll_url($_args);
		return $poll_id;
	}


	/**
	 * search in polls for poll tree
	 */
	public function post_search()
	{
		$repository    = utility::post("repository");
		$search        = utility::post("search");
		$meta          = [];
		$meta['limit'] = 3;
		if($repository == 'personal')
		{
			$meta['user_id'] = $this->login("id");
		}
		else
		{
			$meta['post_sarshomar'] = 1;
		}
		$result = \lib\db\polls::search($search, $meta);
		debug::msg("result", $result);
	}


	/**
	 * udate record of poll meta has saved in option table
	 *
	 * @param      <type>  $_meta_name  The meta name
	 * @param      string  $_status     The status
	 */
	public function update_meta_in_option($_meta_name, $_status = 'enable')
	{
		// get poll meta in option table
		$poll_meta = \lib\db\posts::get_post_meta($this->poll_id);
		$option_id = null;
		foreach ($poll_meta as $key => $value)
		{
			if($value['option_key'] == 'meta' && $value['option_value'] == $_meta_name)
			{
				$option_id = $value['id'];
			}
		}
		if($option_id)
		{
			// update status of meta in option table
			$update = ['option_status' => $_status];
			\lib\db\options::update($update, $option_id);
		}
		elseif($_status == 'enable')
		{
			// insert new record in data in option_table
			$insert_option =
			[
				'post_id'       => $this->poll_id,
				'option_cat'    => "poll_{$this->poll_id}",
				'option_key'    => 'meta',
				'option_value'  => $_meta_name,
				'option_status' => 'enable'
			];
			\lib\db\options::insert($insert_option);
		}
	}


	/**
	 * chekc value posted and update meta
	 *
	 * @param      <type>  $_key    The key
	 * @param      string  $_value  The value
	 */
	function poll_meta_update($_key, $_value)
	{
		$_key = str_replace('meta_', '', $_key);
		if($_value == 'on')
		{
			if(isset($this->poll['meta'][$_key]) && $this->poll['meta'][$_key] == false)
			{
				\lib\db\polls::replace_meta([$_key => true], $this->poll_id);
			}
			else
			{
				\lib\db\polls::merge_meta([$_key => true], $this->poll_id);
			}
			$this->update_meta_in_option($_key, 'enable');
		}
		elseif($_value == '')
		{
			if(isset($this->poll['meta'][$_key]) && $this->poll['meta'][$_key] == true)
			{
				\lib\db\polls::replace_meta([$_key => false], $this->poll_id);
				$this->update_meta_in_option($_key, 'disable');

			}
		}
	}


	/**
	 * update the poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function update_poll($_args)
	{
		// get the poll id
		$poll_id = $this->check_poll_url($_args);
		// save poll id in $this
		$this->poll_id = $poll_id;
		// get poll data
		$poll    = \lib\db\polls::get_poll($poll_id);
		// save poll data in $this
		$this->poll = $poll;
		// get the answrs data
		$answers = \lib\utility\answers::get($poll_id);
		// get the $_POST
		$post    = utility::post();

		// list of value to update
		$update_post = [];

		//check the words of post and set the status
		if(!\lib\db\words::save_and_check($post))
		{
			$update_post['post_status'] = 'awaiting';
		}

		// check all value posted to this page and update if need
		foreach ($post as $key => $value)
		{
			// decode the value posted
			$value = html_entity_decode($value);

			// swich title of post
			switch ($key)
			{
					case 'title':
						// check old title and new title
						if($poll['title'] != $value)
						{
							// update the title
							$update_post['post_title'] = $value;
							$update_post['post_url']   = \lib\db\polls::update_url($poll_id, $value, false);
							$update_post['post_slug']  = \lib\utility\filter::slug($value);
						}
						break;

					case 'description':
						// check the content of poll
						if($poll['content'] != $value)
						{
							$update_post['post_content'] = $value;
						}
						break;

					case 'poll_type':
						// check the poll type
						$type = $this->change_type($value);
						if($poll['type'] != $type)
						{
							$update_post['post_type'] = $type;
						}
						break;

					case 'summary':
						if(!isset($poll['meta']['desc']) || $poll['meta']['desc'] != $value)
						{
							\lib\db\polls::replace_meta(['desc' => $value], $poll_id);
						}
						break;

					case 'sarshomar_knowledge':
						if($this->access('u', 'sarshomar_knowledge', 'add'))
						{
							if($value == 'sarshomar')
							{
								if($poll['sarshomar'] == null)
								{
									$update_post['post_sarshomar'] = 1;
								}
							}
							else
							{
								if($poll['sarshomar'] == '1')
								{
									$update_post['post_sarshomar'] = null;
								}
							}
						}
						break;

					case 'meta_comment':
						if($value == 'on')
						{
							if($poll['comment'] == 'close')
							{
								$update_post['post_comment'] = 'open';
								\lib\db\polls::replace_meta(['comment' => true], $poll_id);
								$this->update_meta_in_option('comment', 'enable');
							}
						}
						elseif($value == '')
						{
							if($poll['comment'] == 'open')
							{
								$update_post['post_comment'] = 'close';
								\lib\db\polls::replace_meta(['comment' => false], $poll_id);
								$this->update_meta_in_option('comment', 'disable');
							}
						}
						break;

					case 'parent_tree_id':
					case 'parent_tree_opt':
						if(utility::post("parent_tree_opt") && utility::post("parent_tree_id"))
						{
							if(utility::post("parent_tree_id") == '')
							{
								\lib\utility\poll_tree::remove($poll_id);
							}
							else
							{
								$tree =
								[
									'parent' => utility::post("parent_tree_id"),
									'child'  => $poll_id,
									'opt'    => utility::post("parent_tree_opt")
								];

								\lib\utility\poll_tree::update($poll_id, $tree);
							}
						}
						break;

					// case 'meta_profile':
					case 'meta_random_sort':
					case 'meta_descriptive':
					case 'meta_score':
					case 'meta_hidden_result':
						$this->poll_meta_update($key, $value);
						break;

					// update the answers
					case substr($key, 0, 7) == 'answers':

						$preg = preg_match("/(.*)\_(\d+)$/", $key, $ans);

						if(!$preg)
						{
							break;
						}

						$type      = $ans[1];
						$id        = intval($ans[2]);
						$opt_index = $id - 1;

						if($type != 'answers')
						{
							break;
						}

						// must update the
						if(isset($answers[$opt_index]['option_value']))
						{
							$option_id = isset($answers[$opt_index]['id']) ? $answers[$opt_index]['id'] : 0;

							if($answers[$opt_index]['option_value'] != $value && $value != '')
							{
								$answer_meta = [];

								// if(isset($answers[$opt_index]['option_meta']['desc']))
								// {
								// 	if(utility::post("answer_desc_". $id) && utility::post("answer_desc_". $id != ''))
								// 	{
								// 		$answer_meta['desc'] = utility::post("answer_desc_". $id);
								// 	}
								// 	else
								// 	{
								// 		$answer_meta['desc'] = $answers[$opt_index]['option_meta']['desc'];
								// 	}
								// }

								$update_answers =
								[
									'option_value' => $value,
									// 'option_meta'  => json_encode($answer_meta, JSON_UNESCAPED_UNICODE)
								];
								\lib\db\options::update($update_answers, $option_id);
							}
							elseif ($value == '')
							{
								if(count($answers) > 2)
								{
									\lib\db\options::update(['option_status' => 'disable'], $option_id);
								}
							}
						}
						else
						{
							if($value != '')
							{
								$answer_meta          = [];
								$answer_meta['type']  = utility::post("answer_type_". $id);
								$answer_meta['point'] = utility::post("answer_point_". $id);
								$answer_meta['true']  = utility::post("answer_true_". $id);
								$answer_meta['desc']  = utility::post("answer_desc_". $id);
								$answer_count = count($answers);
								$answer_count++;
								// must add new answer
								$insert_answer =
								[
									'post_id'       => $poll_id,
									'option_cat'    => "poll_$poll_id",
									'option_key'    => "opt_$answer_count",
									'option_value'  => $value,
									'option_meta'   => json_encode($answer_meta, JSON_UNESCAPED_UNICODE),
									'option_status' => 'enable'
								];
								\lib\db\options::insert($insert_answer);
							}
						}
						break;
					default:
						# code...
						break;
				}
		}
		if(!empty($update_post))
		{
			$update_post = \lib\db\polls::update($update_post, $poll_id);
		}

		// muset be update post meta
		\lib\db\polls::update_answer_in_meta($poll_id);

		if(debug::$status)
		{
			debug::true(T_("all change saved"));
			// get the url
			$url = $this->check_poll_url($_args, "encode");
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
		}
		else
		{
			debug::error(T_("we can not save change on this poll"));
		}
	}


	/**
	 * Removes an answer.
	 */
	function remove_answer($_args)
	{
		$answer_id = utility::post("value");

		$poll_id = $this->check_poll_url($_args);
		if(!$poll_id)
		{
			return true;
		}
		else
		{
			$answers = \lib\utility\answers::get($poll_id);
			if(count($answers) <= 2 )
			{
				return false;
			}
			else
			{
				\lib\db\options::update(['option_status' => 'disable'], $answer_id);
				\lib\db\polls::update_answer_in_meta($poll_id);
				return true;
			}
		}
	}


	/**
	 * get data to add new add
	 */
	function post_add($_args)
	{
		// search in poll
		// for poll tree
		if(utility::post("repository"))
		{
			$this->post_search();
			return;
		}

		// remove one answer
		// the user click on element by class .delete
		if(utility::post("type") == 'remove_answer')
		{
			$this->remove_answer($_args);
			return;
		}

		/**
		 * update the poll or survey
		 */
		if($this->check_poll_url($_args))
		{
			$this->update_poll($_args);
			return;
		}

		// check sarshoamr knowlege permission
		$this->post_sarshomar = null;
		if($this->access('u', 'sarshomar_knowledge', 'add'))
		{
			if(utility::post('sarshomar_knowledge'))
			{
				$this->post_sarshomar = 1;
			}
		}

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
			$answers_in_post = $this->answers_in_post();
			if(utility::post("title") == '' && empty($answers_in_post['answers']))
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
				'user_id'        => $this->login('id'),
				'post_title'     => 'untitled survey',
				'post_privacy'   => 'private',
				'post_type'      => 'survey',
				'post_survey'    => null,
				'post_gender'    => 'survey',
				'post_status'    => 'publish',
				'post_sarshomar' => $this->post_sarshomar
			];
			$survey_id = \lib\db\polls::insert($args);

			// insert the poll
			$insert_poll = $this->insert_poll(['survey_id' => $survey_id]);
			// redirect to @/$url/add to add another poll
			$url = \lib\utility\shortURL::encode($survey_id);
			if($insert_poll)
			{
				// set dashboard data
				\lib\utility\profiles::set_dashboard_data($this->login('id'), 'my_survey');
				$this->redirector()->set_url("@/add/$url");
			}

		}
		elseif(utility::post("add_poll"))
		{
			//users click on this buttom
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			$poll_type   = "survey_poll_"; // need to check
			// get the survey id and survey url
			$survey_id   = $this->check_poll_url($_args, "decode");
			$survey_url  = $this->check_poll_url($_args, "encode");
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
			$loc_id  = utility::post("parent_tree_id");
			if(is_numeric($loc_id))
			{
				$loc_opt = explode(',',utility::post("parent_tree_opt"));
				foreach ($loc_opt as $key => $value)
				{
					$arg =
					[
						'parent' => $loc_id,
						'opt'    => $value,
						'child'  => $insert_poll
					];
					$result = \lib\utility\poll_tree::set($arg);
				}
			}
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
		if(utility::post("secondary-title"))
		{
			// polls::update_url() has retrun  '$/[shortURL of survey id ]/suervy_title'
			$slug = \lib\utility\filter::slug(utility::post("secondary-title"));
			if(strlen($slug) > 100)
			{
				$slug = substr($slug, 0, 99);
			}

			$url = \lib\db\polls::update_url($_survey_id, utility::post("secondary-title"), false);
			if(strlen($url) > 255)
			{
				$url = substr($url, 0, 254);
			}

			$title =  utility::post("secondary-title");
			if(strlen($title) > 200)
			{
				$title = substr($title, 0, 199);
			}

			$args =
			[
				'post_title'  => $title,
				'post_url'    => $url,
				'post_gender' => 'survey',
				'post_slug'   => $slug
			];
			$result = \lib\utility\survey::update($args, $_survey_id);
			if(!$result)
			{
				debug::error(T_("error in save survey title"));
			}

			// save and check words
			if(!\lib\db\words::save_and_check($title))
			{
				\lib\db\survey::update(['post_status' => 'awaiting'], $_survey_id);
				\lib\debug::warn(T_("You have to use words that are not approved in the survery title, Your text comes into review mode", 'secondary-title'));
				\lib\debug::msg('spam', \lib\db\words::$spam);
			}
		}
	}


	/**
	 * check the posted poll type and return the db poll type
	 *
	 * @param      boolean|string  $_poll_type  The poll type
	 *
	 * @return     boolean|string  ( description_of_the_return_value )
	 */
	function change_type($_poll_type)
	{
		$type = false;

		switch ($_poll_type)
		{
			case 'multiple_choice':
				$type = 'select';
				break;

			case 'descriptive':
				$type = 'text';
				break;

			case 'notification':
				$type = 'notify';
				break;

			case 'upload':
				$type = 'upload';
				break;

			case 'starred':
				$type = 'star';
				break;

			case 'numerical':
				$type = 'number';
				break;

			case 'sort':
				$type = 'order';
				break;

			// $poll_type = 'media_image';
			// $poll_type = 'media_video';
			// $poll_type = 'media_audio';

			default:
				$type = false;
				break;
		}
		return $type;
	}


	/**
	 * search in $_POST
	 * and retrun all answer data in post
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	function answers_in_post()
	{
		$answers      =[];
		$answer_true  =[];
		$answer_type  =[];
		$answer_point =[];

		foreach (utility::post() as $key => $value)
		{
			$check = preg_match("/(.*)\_(\d+)$/", $key, $split);
			if($check)
			{
				$type = $split[1];
				$id   = $split[2];
				switch ($type)
				{
					case 'answers':
						$answers[$id] = $value;
						break;

					case 'answer_true':
						$answer_true[$id] = $value;
						break;

					case 'answer_type':
						$answer_type[$id] = $value;
						break;

					case 'answer_point':
						$answer_point[$id] = $value;
						break;
				}
			}
		}

		return
		[
			'answers'      => $answers,
			'answer_true'  => $answer_true,
			'answer_type'  => $answer_type,
			'answer_point' => $answer_point,
		];
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
		$poll_type    = $this->change_type(utility::post("poll_type"));
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
		$title        = utility::post("title");
		// get content
		$content      = utility::post("description");
		// get summary of poll
		$summary      = utility::post("summary");

		// check title
		if($title == null)
		{
			debug::error(T_("poll title can not null"));
			return false;
		}
		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			$summary = substr($summary, 0, 149);
		}

		$publish = 'publish';
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
			'post_privacy'   => 'private',
			'post_comment'   => 'closed',
			'post_status'    => $publish,
			'post_meta'      => "{\"desc\":\"$summary\"}",
			'post_sarshomar' => $this->post_sarshomar
		];
		// inset poll
		$poll_id = \lib\db\polls::insert($args);

		$answers_data = $this->answers_in_post();
		$answers      = $answers_data['answers'];
		$answer_type  = $answers_data['answer_type'];
		$answer_true  = $answers_data['answer_true'];
		$answer_point = $answers_data['answer_point'];

		// check answers
		if($answers)
		{
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
			$answers = \lib\utility\answers::insert($answers_arg);
		}
		else
		{
			debug::error(T_("answers not found"));
			return false;
		}
		// get the metas of this poll
		$metas       = [];
		$post_meta   = [];
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
					if($meta[1] == "hidden_result" && !$this->access('u', 'hidden_result', 'admin'))
					{
						continue;
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

					$post_meta[$meta[1]] = true;
					// comment
					if($meta[1] == 'comment')
					{
						\lib\db\polls::update(['post_comment' => 'open'], $poll_id);
					}
				}
			}
		}
		if($insert_meta)
		{
			$save_poll_metas = \lib\db\options::insert_multi($metas);
			// save meta in post_meta
			$update_post_meta = \lib\db\polls::merge_meta($post_meta, $poll_id);
		}

		if($answers)
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