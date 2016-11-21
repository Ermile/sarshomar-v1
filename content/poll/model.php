<?php
namespace content\poll;
use \lib\utility;

class model extends \mvc\model
{
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


	public function get_poll($_args)
	{
		if(isset($_args->match->url[0]))
		{
			$url = $_args->match->url[0];
		}
		else
		{
			$url = [];
		}

		if(isset($url[1]))
		{
			$sp_ = $url[1];
		}
		else
		{
			$sp_ = null;
		}

		if(isset($url[2]))
		{
			$short_url = $url[2];
		}
		else
		{
			$short_url = null;
		}

		if(isset($url[3]))
		{
			$title = $url[3];
		}
		else
		{
			$title = null;
		}
		if($sp_ == "sp_")
		{
			$poll_id = \lib\utility\shortURL::decode($short_url);
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
			\lib\db\polls::plus_comment($poll_id);
			\lib\debug::true(T_("your comment saved, thank you"));
			return ;
		}
		else
		{
			\lib\debug::error(T_("we can not save your comment, please reload the page and try again"));
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
		// if($result)
		// {
		// 	\lib\debug::true(T_("score saved"));
		// }
		// else
		// {
		// 	\lib\debug::error(T_("score not save"));
		// }
	}

	/**
	 * save poll answers
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_save_answer()
	{
		//----------------------------------------------------------------------------
		// check poll id
		if(isset($_SESSION['last_poll_id']))
		{
			$poll_id = $_SESSION['last_poll_id'];
		}
		else
		{
			\lib\debug::error(T_("we can not find some data, please reload the page and try again"));
			return false;
		}
		//----------------------------------------------------------------------------
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
			$result = \lib\db\polls::faiv_like("like", $this->login('id'), $poll_id);
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
		if(!isset($_SESSION['last_poll_id']) || $poll_id != $_SESSION['last_poll_id'])
		{
			\lib\debug::error(T_("poll id not match with your last question"));
			return false;
		}

		$post = utility::post();
		$opt  = [];

		if(isset($_SESSION['last_poll_opt']) && is_array($_SESSION['last_poll_opt']))
		{
			$session_opt = array_column($_SESSION['last_poll_opt'],'key');
		}

		foreach ($post as $key => $value) {
			if(substr($key, 0,4) == 'opt_')
			{
				if($key == 'opt_other' && $value == '')
				{
					continue;
				}
				$opt[$key] = $value;
			}
			elseif ($key == 'radio')
			{
				if(in_array($value, $session_opt))
				{
					$opt_value   = array_column($_SESSION['last_poll_opt'],'txt', 'key');
					$opt[$value] = $opt_value[$value];
				}
			}
		}

		$result = null;
		if(!empty($opt))
		{
			// check this user answerd to this poll or no
			if(!\lib\utility\answers::is_answered($this->login('id'), $poll_id))
			{
				$result = \lib\utility\answers::save($this->login('id'), $poll_id, $opt, ['answer_txt' => $opt]);
			}
			// if user skip the poll redirect to ask page
			if(isset($opt['opt_0']))
			{
				$this->redirector()->set_url("ask");
			}
		}

		if($result)
		{
			\lib\debug::true(T_("your answer saved"));
			return ;
		}
		else
		{
			\lib\debug::error(T_("error in save your answer"));
			return false;
		}

	}
}
?>