<?php
namespace lib\db\polls;
use \lib\debug;
trait insert
{
	/**
	 * create new poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function create($_args)
	{

		$default_value =
		[
			// get shortURL of poll to update poll
			'update'                          => false,
			// the sarshomar permission fo id of poll
			'permission_sarshomar'            => false,
			// the user can set the profile poll
			'permission_profile'              => false,
			// the user_id has create this poll
			'user'                            => null,
			// title of poll
			'title'                           => null,
			// language of poll
			'language'                        => null,
			// the content
			'description'                     => null,
			// the meta of polls
			'summary'                         => null,
			// the poll type is:
			// multiple
			// descriptive
			// notification
			// upload
			// range
			'type'                            => null,
			// title is image or video or audio
			'multi_media'                     => false,
			// the file path or upload name
			'file'                            => null,
			// the parent
			'parent'                          => null,
			// the publish date
			'publishdate'                     => null,

			// the answers array
			// [
			// 	'txt'                         => 'answer one',
			// 	'type'                        => 'audio'|'emoji',
			// 	'desc'                        => 'description',
			// 	'true'                        => 'true|false',
			// 	'file'                        => file path or upload name
			// 	'score'                       => 10
			// ], ...
			'answers'                         => null,

			// 'privacy'                      => 'public',
			// 'gender'                       => 'poll',
			// is this poll survey or no
			// set the true to create survery
			// set the shortURL of survey to set poll of the survey
			'survey'                          => false,
			// the shortURL of parent poll
			'tree'                            => null,
			// the answers of poll parent
			'tree_answers'                    => null,

			// the options of poll is:
			//
			// tree
			// rangetiming-min
			// rangetiming-max
			// filesize-min
			// filesize-max
			// textlength-min
			// textlength-max
			// numbersize-min
			// numbersize-max
			// starsize-min
			// starsize-max
			// answer
			// choice-count-min
			// choice-count-max
			// random_sort
			// score
			// true_answer
			// descriptive
			// profile
			// hidden_result
			// comment
			// ordering
			// choicemode
			// text_format
			// file_format
			// rangemode
			// text_format_custom
			// file_format_custom
			'options'                         => [],
		];

		$_args = array_merge($default_value, $_args);

		// the shortURL of poll to check if need
		$shortURL = "23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
		/**
		 * check parametr
		 */
		if($_args['update'] !== false && !preg_match("/^[". $shortURL. "]+$/", $_args['update']))
		{
			return \lib\debug::error(T_("invalid parametr update"));
		}

		if(!is_numeric($_args['user']))
		{
			return \lib\debug::error(T_("invalid parametr user"));
		}

		// if(is_null($_args['answers']) || empty($_args['answers']))
		// {
		// 	return \lib\debug::error(T_("invalid parametr answers"));
		// }

		if(!\lib\utility\location\languages::check($_args['language']))
		{
			return \lib\debug::error(T_("invalid parametr language"));
		}

		if(!self::set_db_type($_args['type']))
		{
			return \lib\debug::error(T_("invalid parametr type"));
		}

		// get poll_type
		// the poll type in html code is defrent by the db poll type
		// this function change the html poll type to db poll type
		$poll_type = self::set_db_type($_args['type']);

		// default gender of all post record in sarshomar is 'poll'
		$gender = "poll";

		// check the suevey id to set in post_parent
		if($_args['survey'] && $_args['survey'] !== true)
		{
			$survey_id = $_args['survey_id'];
		}
		else
		{
			$survey_id = null;
		}

		// get title
		$title   = $_args['title'];
		// get content
		$content = $_args['description'];
		// get summary of poll
		$summary = $_args['summary'];

		// check title
		if($title == null)
		{
			return debug::error(T_("Poll title can't be null"), 'title');
		}
		// check lenght of title
		if(strlen($title) > 190)
		{
			return debug::error(T_("Poll title must be less than 190 character"), 'title');
		}

		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			return debug::error(T_("Summery must be less than 150 character"), 'summary');
		}

		$publish = 'draft';
		// save and check words
		if(!\lib\db\words::save_and_check($_args))
		{
			$publish = 'awaiting';
			\lib\debug::warn(T_("You are using an inappropriate word in the text, your poll is awaiting moderation"));
			// plus the userrank of usespamword
			\lib\db\userranks::plus($_args['user'], 'usespamword');
		}

		// get the insert id by check sarshomar permission
		// when not in upldate mode
		$insert_id = null;
		if($_args['update'] === false && $_args['permission_sarshomar'] === true)
		{
			$next_id   = (int) \lib\db\polls::sarshomar_id();
			$insert_id = ++$next_id;
		}

		// ready to inset poll
		$insert_poll =
		[
			'id'			 => $insert_id,
			'user_id'        => $_args['user'],
			'post_title'     => $title,
			'post_type'      => $poll_type,
			'post_content'   => $content,
			'post_language'  => $_args['language'],
			'post_survey'    => $survey_id,
			'post_gender'    => $gender,
			'post_privacy'   => 'public',
			'post_comment'   => 'open',
			'post_status'    => $publish,
			'post_meta'      => "{\"desc\":\"$summary\"}",
			'post_sarshomar' => $_args['permission_sarshomar'] === true ? 1 : 0,
		];

		// start transaction
		\lib\db::transaction();

		// inset poll if we not in update mode
		if($_args['update'] === false)
		{
			$poll_id = self::insert($insert_poll);
		}
		else
		{
			$update_id = \lib\utility\shortURL::decode($_args['update']);
			if(!self::is_my_poll($update_id, $_args['user']))
			{
				return debug::error(T_("This is not your poll, can't update"));
			}
			// in update mode we update the poll
			$poll_id = $update_id;
			array_shift($insert_poll);
			self::update($insert_poll, $poll_id);
		}

		// if in update mode first remoce the answers
		// then set the new answers again
		if($_args['update'] !== false)
		{
			$cat   = "poll_". $poll_id;
			$where =
			[
				'post_id'    => $poll_id,
				'option_cat' => $cat,
				'option_key' => 'opt%'
			];
			\lib\utility\answers::hard_delete($where);
		}

		// the support meta
		// for every poll type we have a list of meta
		// in some mode we needless to answers
		$support_meta = self::meta($poll_type);

		if(in_array('answer', $support_meta))
		{
			// check answers
			if(is_array($_args['answers']))
			{

				$answers = $_args['answers'];
				// remove empty index from answer array
				$answers = array_filter($answers);
				// combine answer type and answer text and answer score
				$combine = [];
				foreach ($answers as $key => $value)
				{
					if(is_array($value))
					{
						$combine[] =
						[
							'true'  => isset($value['true'])  ? $value['true'] 	: null,
							'score' => isset($value['score']) ? $value['score'] : null,
							'type'  => isset($value['type'])  ? $value['type'] 	: null,
							'desc'  => isset($value['desc'])  ? $value['desc'] 	: null,
							'txt'   => isset($value['txt'])   ? $value['txt'] 	: null,
			     		];
					}
				}

				// check the count of answer array
				if(count($combine) < 2)
				{
					return debug::error(T_("You must set two answers"), ['answer1']);
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
				return debug::error(T_("answers not found"), ['answer1', 'answer2']);
			}
		}

		if(!is_null($_args['tree']) && preg_match("/^[". $shortURL. "]+$/", $_args['tree']))
		{
			$loc_id  = \lib\utility\shortURL::decode($_args['tree']);
			if(is_numeric($loc_id))
			{

				$loc_opt = explode(',', $_args['tree_answers']);
				if(is_array($loc_opt))
				{
					foreach ($loc_opt as $key => $value)
					{
						if(substr($value, 0, 4) != 'opt_')
						{
							unset($loc_opt[$key]);
						}
					}
				}
				if(is_array($loc_opt) && count($loc_opt) >= 1)
				{
					if($_args['update'] === true)
					{
						\lib\utility\poll_tree::remove($poll_id);
					}

					foreach ($loc_opt as $key => $value)
					{
						$arg =
						[
							'parent' => $loc_id,
							'opt'    => $value,
							'child'  => $poll_id
						];
						$result = \lib\utility\poll_tree::set($arg);
					}
				}
			}
		}
		elseif(!is_null($_args['tree']))
		{
			return debug::error(T_("invalid parametr tree"));
		}

		// in update mode we first remove all meta of the poll
		// and then insert the meta again
		if($_args['update'] !== false)
		{
			$cat   = "poll_". $poll_id;
			$where =
			[
				'post_id'    => $poll_id,
				'option_cat' => $cat,
				'option_key' => 'meta'
			];
			\lib\utility\answers::hard_delete($where);
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
			if(isset($_args['options'][$value]) && $_args['options'][$value] != '')
			{
				// save the lock of this poll and profile item
				$profile_lock = null;
				$continue     = false;
				switch ($value)
				{
					case 'profile':
						if($_args['options']['profile'])
						{
							if($_args['permission_profile'] === true)
							{
								$profile_lock = $_args['options']['profile'];
							}
						}
						else
						{
							$continue = true;
						}
						break;

					case 'choice-count-min':
					case 'choice-count-min':
						if(isset($_args['options']['choicemode']) && $_args['options']['choicemode'] != 'multi')
						{
							$continue = true;
						}
						break;

					case 'descriptive':
					case 'true_answe':
						if(isset($_args['options']['choicemode']) && $_args['options']['choicemode'] == 'ordering')
						{
							$continue = true;
						}
						break;

					case 'textlength-max':
					case 'textlength-min':
						if(
							isset($_args['options']['text_format']) &&
							($_args['options']['text_format'] != 'any' || $_args['options']['text_format'] != 'number')
						  )
						{
							$continue = true;
						}
						break;



					case 'numbersize-mix':
					case 'numbersize-max':
						if(isset($_args['options']['rangemode']) && $_args['options']['rangemode'] != 'number')
						{
							$continue = true;
						}
						break;

					case 'starsize-mix':
					case 'starsize-max':
						if(isset($_args['options']['rangemode']) && $_args['options']['rangemode'] != 'star')
						{
							$continue = true;
						}
						break;

					default:
						$continue = false;
						break;
				}

				if($continue)
				{
					continue;
				}

				$insert_meta[] =
				[
					'post_id'      => $poll_id,
					'option_cat'   => "poll_$poll_id",
					'option_key'   => 'meta',
					'option_value' => str_replace('-', '_', $value),
					'option_meta'  => $profile_lock
				];

				// save the meta in post_meta fields
				$post_meta[str_replace('-', '_', $value)] = $_args['options'][$value];
				// comment met must be save in post_comment fields
				if($value == 'comment')
				{
					self::update(['post_comment' => 'open'], $poll_id);
				}
			}
		}

		if(!empty($insert_meta))
		{
			// insert the meta in options table
			\lib\db\options::insert_multi($insert_meta);
			// save meta in post_meta fields
			self::merge_meta($post_meta, $poll_id);
		}

		if(\lib\debug::$status)
		{
			// commit code
			\lib\db::commit();

			\lib\utility\profiles::set_dashboard_data($_args['user'], 'my_poll');
			\lib\debug::true(T_("Poll Successfully added"));
			return $poll_id;
		}
		else
		{
			// rollback
			\lib\db::rollback();

			\lib\debug::error(T_("Error in adding poll"));
			return false;
		}
	}


	/**
	 * insert polls as post record
	 * and then insert answers of this poll into answers (options table)
	 *
	 * @param      <type>  $_args  list of polls meta and answers
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args)
	{
		$default_value =
		[
			'id'			   => null,
			'user_id'          => null,
			'post_language'    => null,
			'post_title'       => null,
			'post_slug'        => null,
			'post_url'         => time(). '_'. rand(1,20), // insert post id ofter insert record
			'post_content'     => null,
			'post_type'        => null,
			'post_status'      => 'draft',
			'post_parent'      => null,
			'post_meta'        => null,
			'post_publishdate' => null,
			'post_privacy' 	   => 'public',
			'post_gender'      => 'poll',
			'post_survey'      => null,
		];

		$_args = array_merge($default_value, $_args);

		// check user_id
		if($_args['user_id'] == null)
		{
			return false;
		}

		// check language
		$language = null;
		if($_args['post_language'] == null || $_args['post_language'] === '')
		{
			$language = null;
		}
		else
		{
			if(strlen($_args['post_language']) !== 2)
			{
				$language = \lib\define::get_language();
			}
		}

		// check title
		if($_args['post_title'] == null)
		{
			return false;
		}

		if(strlen($_args['post_title']) > 200)
		{
			$_args['post_title'] = substr($_args['post_title'], 0, 199);
		}

		// get slug string
		if($_args['post_slug'] == null)
		{
			$_args['post_slug'] = \lib\utility\filter::slug($_args['post_title']);
			if(strlen($_args['post_slug']) > 99)
			{
				$_args['post_slug'] = substr($_args['post_slug'], 0, 99);
			}
		}

		// check type
		if($_args['post_type'] == null)
		{
			return false;
		}

		// check status
		if($_args['post_status'] == null)
		{
			$_args['post_status'] = "draft";
		}

		$result = \lib\db\posts::insert($_args);

		// new id of poll, posts.id
		$insert_id 	= \lib\db::insert_id();

		if($insert_id)
		{
			// update post url
			self::update_url($insert_id, $_args['post_title']);
			return $insert_id;
		}
		else
		{
			return false;
		}
	}


	/**
	 * insert quick poll
	 * get title and answers txt then insert
	 * for telegram mode
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function insert_quick($_args)
	{
		if(!isset($_args['user_id']))
		{
			return false;
		}
		else
		{
			$user_id = $_args['user_id'];
		}

		if(!isset($_args['title']))
		{
			return false;
		}
		else
		{
			$title = $_args['title'];
		}

		$post_value =
		[
			'user_id'    => $_args['user_id'],
			'post_title' => $_args['title'],
			'post_type'  => 'select'
		];

		$insert_id = self::insert($post_value);

		if(isset($_args['answers']))
		{
			$answers = array_filter($_args['answers']);
		}
		else
		{
			$answers = null;
		}
		// check insert id and answers exist
		// for example the notify poll has no answerd
		if($insert_id && $answers)
		{
			$answers_value = [];
			foreach ($_args['answers'] as $key => $value)
			{
				$answers_value[] =
				[
					'type' => 'select',
					'txt' => $value
				];
			}
			\lib\utility\answers::insert(['poll_id' => $insert_id , 'answers' => $answers_value]);
		}
		return $insert_id;
	}
}
?>