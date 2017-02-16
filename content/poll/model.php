<?php
namespace content\poll;
use \lib\utility;
use \lib\utility\shortURL;
use \lib\debug;

class model extends \mvc\model
{

	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\poll\answer\tools\add;

	use \content_api\v1\poll\answer\tools\get;

	use \content_api\v1\poll\answer\tools\delete;

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
	public function check_url($_return = false)
	{
		$url     = \lib\router::get_url();
		$poll_id = null;
		if(preg_match("/^sp\_([^\/]*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^\\$([^\/]*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^\\$\/([^\/]*)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^(.*)\/[". utility\shortURL::ALPHABET. "]+$/", $url, $code))
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

		if($_return)
		{
			return $poll_id;
		}

		$poll = \lib\db\polls::get_poll($this->poll_id);

		if(isset($poll['url']) && $poll['url'] != $url .'/' && $poll['url'] != $url)
		{
			$language = null;
			if(isset($poll['language']))
			{
				$language = \lib\define::get_current_language_string($poll['language']);
			}

			$post_url = $poll['url'];

			$preview  = null;

			if(isset($poll['user_id']) && $poll['user_id'] == $this->login('id'))
			{
				$preview = "?preview=yes";
			}

			$new_url = trim($this->url('base'). $language. '/'. $post_url. $preview, '/');

			$this->redirector($new_url)->redirect();
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

			switch (utility::post('setProperty'))
			{
				case 'heart':
					\lib\db\polls::like($this->user_id, utility\shortURL::decode($poll_id), ['debug' => false]);
					break;
				case 'favorite':
					\lib\db\polls::fav($this->user_id, utility\shortURL::decode($poll_id), ['debug' => false]);
					break;
				default:
					debug::error(T_("Can not support this property"), 'setProperty', 'arguments');
					break;
			}
			return;
		}

		// save score of comments
		if(utility::post("type") == 'minus' || utility::post("type") == 'plus')
		{
			$this->save_score_comments();
			return;
		}

		$post = utility::post();

		$opt  = [];

		foreach ($post as $key => $value)
		{
			if(preg_match("/^check\_(\d+)$/", $key, $index))
			{
				if(isset($index[1]))
				{
					$opt[$index[1]] = true;
				}
			}

			if(preg_match("/^radio$/", $key))
			{
				$opt[$value] = true;
				break;
			}
		}

		if(count($opt) === 2 && isset($opt['descriptive']) && isset($opt['radio']))
		{
			$opt[$opt['radio']] = $opt['descriptive'];
		}

		$options           = [];

		$request           = [];
		$request['id']     = $poll_id;
		$request['answer'] = $opt;

		utility::set_request_array($request);

		$is_answerd = $this->poll_answer_get();


		$add    = false;
		$edit   = false;
		$delete = false;

		if(isset($is_answerd['available']))
		{
			if(in_array('add', $is_answerd['available']))
			{
				$add = true;
			}

			if(in_array('edit', $is_answerd['available']))
			{
				$edit = true;
			}

			if(in_array('delete', $is_answerd['available']))
			{
				$delete = true;
			}
		}

		// if($add && empty($opt))
		// {
		// 	return debug::error(T_("You must select one answer or skip the poll"));
		// }

		if($delete && empty($opt))
		{
			$delete = $this->poll_answer_delete(['id' => shortURL::decode($poll_id)]);
			return debug::warn(T_("Your answer has been deleted"));
		}
		elseif($edit && !empty($opt))
		{
			$options['method'] = 'put';
		}
		else
		{
			$options['method'] = 'post';
		}

		$this->poll_answer_add($options);
		// debug::title("");
	}
}
?>