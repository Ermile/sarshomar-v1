<?php
namespace content\poll;
use \lib\utility;
use \lib\utility\shortURL;

class model extends \mvc\model
{

	use \content_api\v1\home\tools\ready;
	use \content_api\v1\poll\tools\get;

	public $poll_code       = null;
	public $poll_id         = null;
	public $get_poll_options =
	[
		'check_is_my_poll'   => false,
		'get_filter'         => true,
		'get_opts'           => true,
		'get_options'        => true,
		'run_options'        => true,
		'get_public_result'  => true,
		'get_advance_result' => true,
		'type'               => null, // ask || random
	];

	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function check_url()
	{
		// $shortURL = $this->controller()::$shortURL;
		// $redirect = false;
		// $url      = null;

		$url     = \lib\router::get_url();
		$poll_id = null;
		if(preg_match("/^sp\_(.*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}

		if(preg_match("/^\\$(.*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}

		if(preg_match("/^\\$\/(.*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		if($poll_id)
		{
			$this->poll_code = $poll_id;
			$this->poll_id = \lib\utility\shortURL::decode($poll_id);
		}
		else
		{
			return false;
		}

		$poll = \lib\db\polls::get_poll($this->poll_id);

		if(isset($poll['url']) && $poll['url'] != $url .'/')
		{
			$language = null;
			if(isset($poll['language']))
			{
				$language = \lib\define::get_current_language_string($poll['language']);
			}

			$post_url = $poll['url'];

			$new_url = trim($this->url('prefix'). $language. '/'. $post_url, '/');
			$this->redirector()->set_url($new_url)->redirect();
		}
		else
		{
			return $poll;
		}
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
		$this->check_url();
		$this->user_id = $this->login('id');

		\lib\utility::set_request_array(['id' => $this->poll_code]);

		$poll = $this->poll_get($this->get_poll_options);
		return $poll;
	}

	/**
	 * Gets the realpath.
	 */
	public function get_realpath()
	{
		$poll = $this->get_posts();
		if(isset($poll['id']))
		{
			$this->user_id = $this->login('id');
			\lib\utility::set_request_array(['id' => \lib\utility\shortURL::encode($poll['id'])]);
			$poll = $this->poll_get($this->get_poll_options);
			return $poll;
		}
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
	 * save poll answers
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_save_answer()
	{

		// save a comment
		if(utility::post("comment"))
		{
			$this->save_comment();
			return;
		}

		//----------------------------------------------------------------------------
		// save heart
		// if(utility::post("type") == 'heart')
		// {
		// 	$rate   = utility::post("data");
		// 	$result = \lib\db\comments::rate($this->login('id'), $poll_id, $rate);
		// 	if($result)
		// 	{
		// 		\lib\debug::true(T_("Your Rate is Saved, Thank You"));
		// 	}
		// 	else
		// 	{
		// 		\lib\debug::error(T_("We can not save your rate, please reload the page and try again"));
		// 	}
		// 	return;
		// }

		// save like
		if(utility::post("type") == 'like')
		{
			$result = \lib\db\polls::like($this->login('id'), $poll_id);
			return;
		}


		//----------------------------------------------------------------------------
		// save score of comments
		if(utility::post("type") == 'minus' || utility::post("type") == 'plus')
		{
			$this->save_score_comments();
			return;
		}


		//----------------------------------------------------------------------------
		// save answers

		if(!$this->login())
		{
			\lib\debug::error(T_("You must login in order to answer the questions"));
			return false;
		}
		\lib\debug::warn(T_("Try later"));
		return;
		// if(!isset($_SESSION['last_poll_id']) || $poll_id != $_SESSION['last_poll_id'])
		// {
		// 	\lib\debug::error(T_("The poll id does not match with your last question"));
		// 	return false;
		// }

		$post = utility::post();

		$opt  = [];
		// $session_opt = [];
		// if(isset($_SESSION['last_poll_opt']) && is_array($_SESSION['last_poll_opt']))
		// {
		// 	$session_opt = array_column($_SESSION['last_poll_opt'],'key');
		// }

		foreach ($post as $key => $value)
		{
			if(preg_match("/^check\_(\d+)$/", $key, $index))
			{
				if(isset($index[1]))
				{
					array_push($opt, $index[1]);
				}
			}

			if(preg_match("/^radio\_(\d+)$/", $key, $index))
			{
				if(isset($index[1]))
				{
					array_push($opt, $index[1]);
					break;
				}
			}
		}

		// var_dump($opt);
		// exit();
		$result = ['status' => false, 'msg' => T_("Error in saving your answers")];
		if(!empty($opt))
		{
			$result = \lib\utility\answers::save(
					// the user id
					$this->login('id'),
					// the poll id
					$poll_id,
					// list of answer keys
					array_keys($opt),
					// answer txt
					['answer_txt' => $opt]
					);

			// if user skip the poll redirect to ask page
			if(isset($opt[0]))
			{
				$this->redirector()->set_url("ask");
			}

			if($result->is_ok())
			{
				\lib\debug::true($result->get_message());
				return ;
			}
			else
			{
				\lib\debug::error($result->get_message());
				return false;
			}
		}
		else
		{
			\lib\debug::error(T_("You must select one answer or skip the poll"));
			return false;
		}

	}
}
?>