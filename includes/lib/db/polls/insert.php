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
			// the file path
			'file_path'                       => null,
			// the upload name
			'upload_name'                     => null,
			// answers of poll
			'answers'                         => null,
			// the options of poll:
			'options'                         => [],
			// filters
			'filters'                         => [],
		];
		$_args = array_merge($default_value, $_args);
		
		// the shortURL of poll to check if need
		$shortURL = "23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
		/**
		 * check parametr
		 */
		// update id must be a shortURL
		if($_args['update'] !== false && !preg_match("/^[". $shortURL. "]+$/", $_args['update']))
		{
			return \lib\debug::error(T_("invalid parametr update"));
		}

		// check user id. 
		if(!is_numeric($_args['user']))
		{
			return \lib\debug::error(T_("invalid parametr user"));
		}
		// check answers
		if(is_null($_args['answers']) || empty($_args['answers']) || !is_array($_args['answers']))
		{
			return \lib\debug::error(T_("invalid parametr answers"));
		}

		// check answers	
		if(!isset($_args['answers'][0]['type']) || (isset($_args['answers'][0]['type']) && empty($_args['answers'][0]['type'])))
		{
			return \lib\debug::error(T_("invalid parametr answer type"));
		}
		// set the answer type
		$answer_type = $_args['answers'][0]['type'];
		if(
			!isset($_args['answers'][0][$answer_type]) || 
			(
				isset($_args['answers'][0][$answer_type]) && 
				!is_array($_args['answers'][0][$answer_type])
			)
		  )
		{
			return \lib\debug::error(T_("invalid object of answer type"));
		}
		// get options of answer type
		$answer_type_object = $_args['answers'][0][$answer_type];

		// check language
		$language = null;
		if(	
			!isset($_args['options']['language']) || 
			(
				isset($_args['options']['language']) && 
				!\lib\utility\location\languages::check($_args['options']['language'])
			)
		  )
		{
			return \lib\debug::error(T_("invalid parametr language"));
		}

		// check the suevey id to set in post_parent
		if(
			isset($_args['options']['survey_id']) && 
			$_args['options']['survey_id'] && 
			!preg_match("/^[". $shortURL. "]+$/", $_args['options']['survey_id'])
		  )
		{
			return \lib\debug::error(T_("invalid parametr survey_id"));
		}

		// default gender of all post record in sarshomar is 'poll'
		$gender    = "poll";
		$survey_id = null;

		if(isset($_args['options']['survey_id']))
		{
			$survey_id = \lib\utility\shortURL::decode($_args['options']['survey_id']);
			$gender    = "survey";
		}

		// the post meta as json
		$post_meta         = [];

		// get title
		$title             = $_args['title'];
		// get content
		$content           = null;
		if(isset($_args['options']['description']))
		{
			$content       = $_args['options']['description'];
		}

		// get summary of poll
		$summary           = null;
		if(isset($_args['options']['summary']))
		{
			$summary = $_args['options']['summary'];
		}

		$post_meta['desc'] = $summary;

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
			return debug::error(T_("Summery must be less than 150 character"), 'summary', 'options');
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

		// start transaction
		\lib\db::transaction();

		/**
		 * upload files of poll title
		 */
		if((isset($_args['upload_name']) && $_args['upload_name']) || (isset($_args['file_path']) && $_args['file_path']))
		{
			$upload_args =
			[
				'user_id'     => $_args['user'],
				'upload_name' => $_args['upload_name'],
				'file_path'   => $_args['file_path']
			];

			$file_title = \lib\utility\upload::upload($upload_args);
			
			if(\lib\debug::get_msg("result"))
			{
				$post_meta['attachment_id'] = \lib\debug::get_msg("result");
			}
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
			'post_type'      => $gender,
			'post_content'   => $content,
			'post_language'  => $_args['options']['language'],
			'post_survey'    => $survey_id,
			'post_gender'    => $gender,
			'post_privacy'   => 'private',
			'post_comment'   => 'open',
			'post_status'    => $publish,
			'post_meta'      => json_encode($post_meta, JSON_UNESCAPED_UNICODE),
			'post_sarshomar' => $_args['permission_sarshomar'] === true ? 1 : 0,
		];

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

			$old_post_meta            = \lib\db\polls::get_poll_meta($poll_id);
			$post_meta                = array_merge($old_post_meta, $post_meta);
			$insert_poll['post_meta'] = json_encode($post_meta, JSON_UNESCAPED_UNICODE);

			array_shift($insert_poll);
			self::update($insert_poll, $poll_id);
		}

		

		$answers = $_args['answers'];
		// remove empty index from answer array
		$answers = array_filter($answers);

		// combine answer type and answer text and answer score
		$combine = [];
		foreach ($answers as $key => $value)
		{
			$title = null;
			if(isset($value['title']) && $value['title'])
			{
				$title = $value['title'];
			}

			$type  = null;
			if(isset($value['type']))
			{
				switch ($value['type'])
				{
					case 'select':
					case 'emoji':
					case 'descriptive':
					case 'upload':
					case 'range':
					case 'notification':
						$type = $value['type'];
						break;
					
					default:
						return debug::error(T_("invalid parametr type (:type) in index :key of answer", ['key' => $key, 'type' => $value['type']]),false, 'answers');
						break;
				}
			}
			else
			{
				return debug::error(T_("invalid parametr answer type in index :key of answer", ['key' => $key]), false, 'answers');
			}

			$attachment_id = null;
			if(isset($value['file']) && $value['file'])
			{
				$upload_answer = 
				[
					'upload_name' => $value['file'], 
					'file_path'   => $value['file'], 
					'user_id'     => $_args['user']
				];
				
				$upload_answer = \lib\utility\upload::upload($upload_answer);
				if(\lib\debug::get_msg("result"))
				{
					$attachment_id = \lib\debug::get_msg("result");
				}
			}
			$combine[$key] =
			[
				'txt'           => $title,
				'type'          => $value['type'],
				'desc'          => isset($value['description'])  	? $value['description'] 	: null,
				'attachment_id' => $attachment_id,
     		];

			// 'meta'          => $answer_meta,
			// 'true'          => isset($answer_meta['is_true'])  	? $answer_meta['is_true'] 	: null,
			// 'score'         => isset($answer_meta['score']) 	? $answer_meta['score'] 	: null,

			$support_answer_object = self::support_answer_object($value['type']);
			$answer_meta           = [];

			if(isset($value[$value['type']]))
			{	
				foreach ($support_answer_object as $index => $reg) 
				{
					if(isset($value[$value['type']][$index]))
					{
						if(preg_match($reg, $value[$value['type']][$index]))
						{
							switch ($index) 
							{
								case 'value':
									# code...
									break;
								
								default:
									# code...
									break;
							}
							$answer_meta[$index] = $value[$value['type']][$index];
						}
					}
				}
			}
	

     		if($value['type'] != 'select')
     		{
     			break;
     		}
			
		}
		// check the count of answer array
		if($answer_type == 'select' && count($combine) < 2)
		{
			return debug::error(T_("You must set two answers"), ['answer1', 'answer2'], 'answers');
		}

		$answers_arg =
		[
			'poll_id' => $poll_id,
			'answers' => $combine,
			'update'  => $_args['update'],
		];
		$answers = \lib\utility\answers::insert($answers_arg);

		if(
			isset($_args['options']['tree']['parent_id']) && 
			$_args['options']['tree']['parent_id'] && 
			preg_match("/^[". $shortURL. "]+$/", $_args['options']['tree']['parent_id'])
		  )
		{
			$loc_id  = \lib\utility\shortURL::decode($_args['options']['tree']['parent_id']);
			if(is_numeric($loc_id))
			{
				$loc_opt = explode(',', $_args['options']['tree']['answers']);
				if(is_array($loc_opt))
				{
					foreach ($loc_opt as $key => $value)
					{
						if(!is_numeric($value))
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
		elseif($_args['options']['tree']['parent_id'])
		{
			return debug::error(T_("invalid parametr tree"));
		}

		// insert filters
		if(isset($_args['filters']) && is_array($_args['filters']))
		{
			$insert_filters = \lib\utility\postfilters::update($_args['filters'], $poll_id);
			/**
			 * set ranks
			 * plus (int) member in member field
			 */
			if(isset($_args['filters']['max_person']))
			{	
				$member = (int) $_args['filters']['max_person'];
				$member_exist = (int) \lib\db\users::get_count("awaiting");
				if($member <= $member_exist)
				{
					\lib\db\ranks::plus($poll_id, "member", intval($member), ['replace' => true]);
				}
				else
				{
					return debug::error(T_(":max user was found, low  the slide of members ",["max" => $member_exist]));
				}
			}
		}

		/**
			T_("Poll Successfully added");
			T_("Poll Successfully edited");
			T_("Error in adding poll");
			T_("Error in editing poll");
		 */
		$msg_mod = "add";
		if($_args['update'])
		{
			$msg_mod = "edit";
		}

		if(\lib\debug::$status)
		{
			// commit code
			\lib\db::commit();
			\lib\utility\profiles::set_dashboard_data($_args['user'], 'my_poll');
			\lib\debug::true(T_("Poll Successfully {$msg_mod}ed"));
			return ['id' => \lib\utility\shortURL::encode($poll_id)];
		}
		else
		{
			// rollback
			\lib\db::rollback();
			\lib\debug::error(T_("Error in {$msg_mod}ing poll"));
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