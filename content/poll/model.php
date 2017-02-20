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

	use \content_api\v1\poll\stats\tools\get;

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

			$new_url = trim($this->url('base'). $language. '/'. $post_url, '/');

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
			utility::set_request_array(['id' => $poll_id]);
			$result = $this->poll_stats();

			switch (utility::get('chart'))
			{
				// case 'gender':

				case 'result':
					if(isset($result['total']))
					{
						$result = $result['total'];
					}
					break;

				default:
					$result = $this->am_chart($result, utility::get("chart"));
					break;
			}
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
		if(isset($avalible['available']) && empty($avalible['available']))
		{
			return true;
		}
		return false;
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

		debug::title(T_("Operation faild"));

		// save score of comments
		if(utility::post("type") == 'minus' || utility::post("type") == 'plus')
		{
			$this->save_score_comments();
			return;
		}

		$data = '{}';

		if(isset($_POST['data']))
		{
			$data = $_POST['data'];
		}

		$data    = json_decode($data, true);
		$request = utility\safe::safe($data);

		if(!isset($data['answer']) && !isset($data['skip']))
		{
			debug::error(T_("You must set answer or skip the poll"));
			return false;
		}

		$request['id']     = $poll_id;

		$options           = [];

		utility::set_request_array($request);

		if(utility\answers::is_answered($this->user_id, shortURL::decode($poll_id)))
		{
			$options['method'] = 'put';
		}
		else
		{
			$options['method'] = 'post';
		}

		$this->poll_answer_add($options);
	}


	/**
	 * change stats result in am
	 *
	 * @param      <type>  $_poll_data  The poll data
	 */
	public function am_chart($_poll_data, $_type)
	{
		// var_dump(...func_get_args());
		// exit();
		$advance_stats_title = [];

		if(isset($_poll_data['advance_stats']['valid'][$_type]) && is_array($_poll_data['advance_stats']['valid'][$_type]))
		{
			$advance_stats_result = [];
			foreach ($_poll_data['advance_stats']['valid'][$_type] as $opt_key => $value)
			{
				if(is_array($value))
				{
					foreach ($value as $filter => $count)
					{
						$advance_stats_result[$opt_key]["valid_". $filter] = $count;
					}
				}
			}
		}

		if(isset($_poll_data['advance_stats']['invalid'][$_type]) && is_array($_poll_data['advance_stats']['invalid'][$_type]))
		{
			foreach ($_poll_data['advance_stats']['invalid'][$_type] as $opt_key => $value)
			{
				if(is_array($value))
				{
					foreach ($value as $filter => $count)
					{
						$advance_stats_result[$opt_key]["invalid_". $filter] = $count;
					}
				}
			}
		}

		foreach ($advance_stats_result as $key => $value)
		{
			$advance_stats_result[$key]['key'] = $key;
		}
		sort($advance_stats_result);
		return $advance_stats_result;
	}
}
?>