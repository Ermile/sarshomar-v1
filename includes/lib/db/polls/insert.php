<?php
namespace lib\db\polls;
use \lib\debug;

trait insert
{
	protected static $args           = [];
	protected static $permission     = [];
	protected static $debug          = true;
	protected static $method         = 'post';

	protected static $draft_mod      = true;
	protected static $publish_mod    = false;

	protected static $update_mod     = false;
	protected static $poll_id        = false;

	protected static $old_saved_poll = [];

	protected static $user_id        = false;
	protected static $real_user_id   = null;
	protected static $old_status     = null;

	protected static $poll_full_url  = null;

	protected static $answer_count   = 0;

	protected static $update_posts   = [];

	use insert\check;
	use insert\reset;
	use insert\max;
	use insert\answers;
	use insert\options;
	use insert\poll;


	/**
	 * create new poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function create($_args = [])
	{

		$default_value =
		[
			// get shortURL of poll to update poll
			'update'      => false,
			// the user_id has create this poll
			'user'        => null,
			// the upload name
			'upload_name' => null,
			// the short url
			'shortURL'    => \lib\utility\shortURL::ALPHABET,
			// enable debug mode
			'debug'       => true,
			// method
			'method'      => 'post',
		];

		$_args = array_merge($default_value, $_args);

		if($_args['debug'] === false)
		{
			self::$debug = false;
		}

		// // permission list
		// self::$permission = $_args['permission'];

		// set args
		self::$args = $_args;

		// the shortURL of poll to check if need
		$shortURL = self::$args['shortURL'];
		self::$real_user_id = self::$args['user'];
		// update id must be a shortURL
		if(self::$args['update'] !== false && !preg_match("/^[". $shortURL. "]+$/", self::$args['update']))
		{

			// \lib\db::rollback();
			\lib\db\logs::set('system:poll:invalid_update_parameter', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("Invalid update parameter"), 'update', 'system');
		}
		elseif(self::$args['update'])
		{
			self::$update_mod     = true;
			self::$poll_id        = \lib\utility\shortURL::decode(self::$args['update']);
			self::$old_saved_poll = \lib\db\polls::get_poll(self::$poll_id);
			self::$poll_full_url  = isset(self::$old_saved_poll['url']) ? self::$old_saved_poll['url']: null;
			self::$old_status     = isset(self::$old_saved_poll['status']) ? self::$old_saved_poll['status'] : null;
			self::$real_user_id   = isset(self::$old_saved_poll['user_id']) ? self::$old_saved_poll['user_id'] : null;
			if(self::$old_status !== 'draft' && !self::poll_check_permission('admin'))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:edit:error:status', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Can not edit poll, the status of this poll is :status", ['status' => self::$old_status]), 'status', 'permission');
			}
		}

		// check user id.
		if(!is_numeric(self::$args['user']))
		{
			if(self::$debug)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('system:poll:invalid_user_parameter', self::$args['user'], ['meta' => ['input' => self::$args]]);
				debug::error(T_("Invalid user paramater"), 'user', 'system');
			}
			return;
		}

		self::$user_id   = self::$args['user'];

		self::$method    = mb_strtolower($_args['method']);

		self::$draft_mod = true;

		// self::$args['status'] = 'draft';

		// if in update mod check permission on editing this poll
		if(self::$update_mod)
		{
			if(!self::is_my_poll(self::$poll_id, self::$user_id) && !self::poll_check_permission('admin'))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:permission:poll:edit', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("This is not your poll, can't update"), 'id', 'permission');
			}
		}

		// check empty poll
		if(self::empty_poll() && self::$method == 'post')
		{
			if(self::$debug)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:edit:error:empty_poll', self::$args['user'], ['meta' => ['input' => self::$args]]);
				debug::error(T_("Title and at least two answers is needed to submit the poll"), 'permission', 'arguments');
			}
			return false;
		}

		if(!self::$update_mod)
		{
			// check max draft count of every user
			self::max_draft();

			if(!debug::$status)
			{
				return false;
			}
		}

		// insert poll record
		self::insert_poll();

		// insert answers of poll
		self::insert_answers();

		// insert options of poll
		self::insert_options();

		if(!empty(self::$update_posts))
		{
			\lib\db\polls::update(self::$update_posts, self::$poll_id);
		}

		// T_("Poll added successfully");
	 	// T_("Poll edited successfully");
	 	// T_("Error in adding poll");
	 	// T_("Error in editing poll");

		$msg_mod = "add";
		if($_args['update'])
		{
			$msg_mod = "edit";
		}

		if(\lib\debug::$status)
		{
			if(!self::$update_mod)
			{
				\lib\db\logs::set('user:poll:add', self::$args['user'], ['meta' => ['input' => self::$args]]);
				\lib\utility\profiles::set_dashboard_data($_args['user'], 'my_poll');
			}

			debug::title(T_("Poll :operation successfully", ['operation' => $msg_mod. 'ed']));

			if(self::$debug)
			{
				// debug::true(T_("Poll Successfully {$msg_mod}ed"));
			}
			$id        = \lib\utility\shortURL::encode(self::$poll_id);
			$host      = Protocol."://" . \lib\router::get_root_domain();
			$short_url = $host. '/$'. $id;
			$url       = $host. '/'. self::$poll_full_url;
			return
			[
				'id'        => $id,
				'url' 		=> $url,
				'short_url' => $short_url,
			];
		}
		\lib\db\logs::set('user:poll:add:fail', self::$args['user'], ['meta' => ['input' => self::$args]]);
		debug::title(T_("Error in :operation poll", ['operation' => $msg_mod.'ing']));
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


		if(mb_strlen($_args['post_title']) > 200)
		{
			$_args['post_title'] = substr($_args['post_title'], 0, 199);
		}

		// get slug string
		if($_args['post_slug'] && mb_strlen($_args['post_slug']) > 99)
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
				return debug::error(T_("Can not add poll"), false, 'sql');
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