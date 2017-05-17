<?php
namespace content\poll;
use \lib\utility;
use \lib\utility\shortURL;
use \lib\debug;

class model extends \content\home\model
{

	use \content_api\v1\home\tools\ready;
	use \content_api\v1\poll\tools\get;
	use \content_api\v1\poll\answer\tools\add;
	use \content_api\v1\poll\answer\tools\get;
	use \content_api\v1\poll\answer\tools\delete;
	use \content_api\v1\poll\stats\tools\get;
	use \content_api\v1\poll\status\tools\get;

	public $get_poll_options =
	[
		'check_is_my_poll'   => false,
		'get_filter'         => true,
		'get_opts'           => true,
		'get_options'        => true,
		'run_options'        => true,
		'get_public_result'  => true,
		'get_advance_result' => true,
		'load_from_site'     => true,
		'type'               => null, // ask || random
	];

	public function status_avalible($_poll_code)
	{
		utility::set_request_array(['id' => $_poll_code]);
		$avalible = $this->poll_status();
		return $avalible;
	}

	/**
	 * Gets the comments.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   The comments.
	 */
	public function get_comments($_args)
	{
		$poll_id = $_args->match->url[0][2];
		$poll_id = utility\shortURL::decode($poll_id);
		$comment_list = \lib\db\comments::get_post_comment($poll_id, 50, $this->login('id'));
		if(!$comment_list)
		{
			return [];
		}
		return $comment_list;
	}


	/**
	 * Gets the poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_poll($_args)
	{
		if($this->draw_stats())
		{
			return ;
		}

		$this->check_url();
		$this->user_id = $this->login('id');
		if($this->poll_code)
		{
			\lib\utility::set_request_array(['id' => $this->poll_code]);

			$poll = $this->poll_get($this->get_poll_options);
			return $poll;
		}
	}

	/**
	 * Gets the realpath.
	 */
	public function get_realpath()
	{
		if($this->draw_stats())
		{
			return ;
		}
		$check_status = $this->access('admin:admin:view') ? false : true ;
		$load_poll =
		[
			'post_status'    => $this->controller()::$accept_poll_status,
			'check_status'   => $check_status,
			'check_language' => false,
			'post_type'      => ['poll', 'survey']
		];
		$poll = $this->get_posts(false, null, $load_poll);
		if(isset($poll['id']))
		{
			$this->user_id = $this->login('id');
			\lib\utility::set_request_array(['id' => \lib\utility\shortURL::encode($poll['id'])]);
			$poll = $this->poll_get($this->get_poll_options);
			return $poll;
		}
	}


	/**
	 * Draws statistics.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function draw_stats()
	{
		if(utility::get('chart'))
		{
			$poll_id       = $this->check_url(true);
			$this->user_id = $this->login('id');
			$request = [];
			$request['id'] = $poll_id;

			switch (utility::get('chart'))
			{
				case 'result':
					// no thing...
					break;

				default:
					$request['type'] = utility::get('chart');
					break;
			}

			utility::set_request_array($request);
			$result = $this->poll_stats();
			$result = $this->am_chart($result , utility::get('chart'));
			debug::msg('list', json_encode($result, JSON_UNESCAPED_UNICODE));
			return true;
		}
		return false;
	}


	/**
	 * Saves a comment.
	 */
	public function save_comment()
	{
		$result = false;
		$user_id = $this->login("id");
		$poll_id = $_SESSION['last_poll_id'];

		$type    = 'comment';
		$status  = 'unapproved';
		$content = utility::post("content");
		$rate    = utility::post('rate');
		if(intval($rate) > 5)
		{
			$rate = 5;
		}
		if($content != '')
		{

			$args =
			[
				'comment_author'  => $this->login("displayname"),
				'comment_email'   => $this->login("email"),
				'comment_content' => $content,
				'comment_type'    => $type,
				'comment_status'  => $status,
				'user_id'         => $user_id,
				'post_id'         => $poll_id,
				'comment_meta'    => $rate
			];
			// insert comments
			$result = \lib\db\comments::insert($args);
		}
		if(intval($rate) > 0)
		{
			$result = \lib\db\comments::rate($this->login('id'), $poll_id, $rate);
		}

		if($result)
		{
			// save comment count to dashboard
			\lib\utility\profiles::set_dashboard_data($user_id, 'comment_count');
			\lib\db\ranks::plus($poll_id, 'comment');
			\lib\debug::true(T_("Comment saved, Thank you"));
			return ;
		}
		else
		{
			\lib\debug::error(T_("We Couldn't save your comment, Please reload the page and try again"));
			return false;
		}
	}


	/**
	 * Saves score comments.
	 */
	public function save_score_comments()
	{
		$user_id    = $this->login("id");
		$type       = utility::post("type");
		$comment_id = utility::post("data");
		$result = \lib\db\commentdetails::set($user_id, $comment_id, $type);
	}


	/**
	 * get avalible answer to this poll
	 */
	public function answer_lock($_poll_code)
	{
		utility::set_request_array(['id' => $_poll_code]);
		$avalible = $this->poll_answer_get();
		return $avalible;
	}


	/**
	 * save poll answers
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_save_answer()
	{
		if(!$this->login())
		{
			\lib\debug::error(T_("You must login in order to answer the questions"));
			return false;
		}

		$poll_id = $this->check_url(true);

		$this->user_id = $this->login('id');

		if(utility::post("setProperty"))
		{
			$mode = null;
			if(utility::post('status') == 'true')
			{
				$mode = true;
			}
			elseif(utility::post('status') == 'false')
			{
				$mode = false;
			}
			else
			{
				return debug::error(T_("Invalid parameter status"), 'setProperty', 'status');
			}

			$poll_id = utility\shortURL::decode($poll_id);

			switch (utility::post('setProperty'))
			{
				case 'heart':
					\lib\db\polls::like($this->user_id, $poll_id, ['debug' => false]);
					break;

				case 'favorite':
					\lib\db\polls::fav($this->user_id, $poll_id, ['debug' => false]);
					break;

				// case 'rate':
				// 	\lib\db\comments::rate($this->user_id, $poll_id, utility::post("rate"));
				// 	break;

				default:
					debug::error(T_("Can not support this property"), 'setProperty', 'arguments');
					break;
			}
			return;
		}

		debug::title(T_("Operation faild"));

		$data = '{}';

		if(isset($_POST['data']))
		{
			$data = $_POST['data'];
		}

		$data    = json_decode($data, true);
		$request = utility\safe::safe($data);


		$request['id']     = $poll_id;

		$options           = [];

		utility::set_request_array($request);
		$is_answered = utility\answers::is_answered($this->user_id, shortURL::decode($poll_id));
		if($is_answered)
		{
			$options['method'] = 'put';
		}
		else
		{
			$options['method'] = 'post';
		}

		if(!isset($data['answer']) && !isset($data['skip']))
		{
			return debug::error(T_("You must set answer or skip the poll"));
		}
		elseif(isset($data['answer']) && empty($data['answer']))
		{
			if($is_answered)
			{
				$this->poll_answer_delete(['id' => $poll_id]);
				// muset remove her answer
				if(\lib\debug::$status)
				{
					debug::warn(T_("Answer saved"));
				}
				return;
			}
		}

		$this->poll_answer_add($options);
	}


	/**
	 * change stats result in am
	 *
	 * @param      <type>  $_poll_data  The poll data
	 */
	public function am_chart($_stats, $_type)
	{
		$_type = (string) $_type;

		$temp_cats = [];
		$return    = [];
		if(isset($_stats['answers']) && is_array($_stats['answers']))
		{
			$cats = array_column($_stats['answers'], 'cats');
			foreach ($cats as $key => $value)
			{
				if(is_array($value))
				{
					$temp_cats = array_merge($temp_cats, $value);
				}
			}
			$temp_cats = array_unique($temp_cats);
		}
		// add trans
		$_stats['trans'] = [];

		foreach ($temp_cats as $key => $value)
		{
			$_stats['trans'][$value] = ucfirst(T_($value));
		}
		if(!$temp_cats && !$_stats['trans'])
		{
			$_stats['trans']['value_reliable']   = ucfirst(T_('reliable'));
			$_stats['trans']['value_unreliable'] = ucfirst(T_('unreliable'));
		}

		foreach ($temp_cats as $i => $cat)
		{
			if(isset($_stats['answers']) && is_array($_stats['answers']))
			{
				foreach ($_stats['answers'] as $k => $data)
				{
					if(isset($data[$_type]) && is_array($data[$_type]))
					{
						foreach ($data[$_type] as $j => $value)
						{
							if(isset($value['title']) && $value['title'] == $cat)
							{
								$_stats['answers'][$k][$cat]                = (isset($value['value'])) ? $value['value'] : 0;
								$_stats['answers'][$k][$cat. "_reliable"]   = (isset($value['value_reliable'])) ? $value['value_reliable'] : 0;
								$_stats['answers'][$k][$cat. "_unreliable"] = (isset($value['value_unreliable'])) ? $value['value_unreliable'] : 0;
							}
						}
					}
				}
			}
		}
		return $_stats;
	}
}
?>