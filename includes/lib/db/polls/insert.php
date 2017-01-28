<?php
namespace lib\db\polls;
use \lib\debug;

trait insert
{
	protected static $args          = [];

	protected static $draft_mod     = true;
	protected static $publish_mod   = false;

	protected static $update_mod    = false;
	protected static $poll_id       = false;

	protected static $user_id       = false;


	use insert\max;
	use insert\answers;
	use insert\check;
	use insert\options;
	use insert\poll;


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
			// poll type [poll|survey]
			'type'							  => 'poll',
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
			// the short url
			'shortURL'						  => \lib\utility\shortURL::ALPHABET,
			// enable debug mode
			'debug' 						  => true,
		];

		$_args = array_merge($default_value, $_args);

		// set args
		self::$args = $_args;

		// the shortURL of poll to check if need
		$shortURL = self::$args['shortURL'];

		// update id must be a shortURL
		if(self::$args['update'] !== false && !preg_match("/^[". $shortURL. "]+$/", self::$args['update']))
		{
			return debug::error(T_("Invalid parametr update"), 'update', 'system');
		}
		elseif(self::$args['update'])
		{
			self::$update_mod = true;
			self::$poll_id    = \lib\utility\shortURL::decode(self::$args['update']);
		}

		// check user id.
		if(!is_numeric(self::$args['user']))
		{
			return debug::error(T_("Invalid parametr user"), 'user', 'system');
		}

		self::$user_id = self::$args['user'];

		// set status mod
		if(isset(self::$args['options']['status']))
		{
			if(self::$args['options']['status'] == 'publish')
			{
				self::$publish_mod = true;
				self::$draft_mod   = false;
			}
			elseif(self::$args['options']['status'] == 'draft')
			{
				self::$publish_mod = false;
				self::$draft_mod   = true;
			}
			elseif(self::$args['options']['status'])
			{
				return debug::error(T("Invalid parametr status"), 'options', 'arguments');
			}
		}
		else
		{
			self::$draft_mod = true;
		}

		// if in update mod check permission on editing this poll
		if(self::$update_mod)
		{
			if(!self::is_my_poll(self::$poll_id, self::$user_id))
			{
				return debug::error(T_("This is not your poll, can't update"), 'id', 'permission');
			}
		}

		// check max draft count of every user
		self::max_draft();

		// insert poll record
		self::insert_poll();

		// insert answers of poll
		self::insert_answers();

		// insert options of poll
		self::insert_options();

		// check poll
		// if in publish mod and have error return the error
		// if in draft mod return no error
		self::check();
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
			if(!self::$update_mod)
			{
				\lib\utility\profiles::set_dashboard_data($_args['user'], 'my_poll');
			}
			if($_args['debug'])
			{
				debug::true(T_("Poll Successfully {$msg_mod}ed"));
			}
			return ['id' => \lib\utility\shortURL::encode(self::$poll_id)];
		}
		return false;
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
			'post_type'        => 'poll',
			'post_status'      => 'draft',
			'post_parent'      => null,
			'post_meta'        => null,
			'post_publishdate' => null,
			'post_privacy' 	   => 'public',
			'post_survey'      => null,
		];

		$_args = array_merge($default_value, $_args);


		if(strlen($_args['post_title']) > 200)
		{
			$_args['post_title'] = substr($_args['post_title'], 0, 199);
		}

		// get slug string
		if($_args['post_slug'] && strlen($_args['post_slug']) > 99)
		{
			$_args['post_slug'] = substr($_args['post_slug'], 0, 99);
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
			self::update_url($insert_id, $_args['post_slug']);
			return $insert_id;
		}
		else
		{
			if(debug::$status)
			{
				return debug::error(T_("Cann't add poll"), false, 'sql');
			}
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