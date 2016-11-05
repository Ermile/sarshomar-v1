<?php
namespace content\poll;
use \lib\utility;

class model extends \mvc\model
{
	public function get_all()
	{
		// echo ("All Questions is here || random result is here or other");
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
	 * save poll answers
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_save_answer()
	{
		if(utility::post("comment"))
		{
			if(isset($_SESSION['last_poll_id']))
			{
				$poll_id = $_SESSION['last_poll_id'];
			}
			elseif(utility::post("data-id") && utility::post("data-id") != '')
			{
				$poll_id = utility::post("data-id");
			}
			else
			{
				\lib\debug::error(T_("we can not save your comment, please reload the page and try again"));
				return false;
			}
			$rate = utility::post("rate");
			if(intval($rate) > 5)
			{
				$rate = 5;
			}

			$args =
			[
				'comment_author'  => $this->login("displayname"),
				'comment_email'   => $this->login("email"),
				'comment_meta'    => utility::post("title"),
				'comment_content' => utility::post("content"),
				'comment_rate'    => $rate,
				'user_id'         => $this->login("id"),
				'post_id'         => $poll_id
			];
			// insert comments
			$result = \lib\db\comments::insert($args);

			if($result)
			{
				\lib\debug::true(T_("your comment saved, tank you"));
				return ;
			}
			else
			{
				\lib\debug::error(T_("we can not save your comment, please reload the page and try again"));
				return false;
			}
			return;
		}
		if(utility::post("data-id") && utility::post("data-id") != '')
		{
			$poll_id = utility::post("data-id");
		}
		else
		{
			\lib\debug::error(T_("poll id not found"));
			return false;
		}

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
			$result = \lib\db\answers::save($this->login('id'), $poll_id, $opt, $opt);
		}

		if($result)
		{
			$next_url = \lib\db\polls::get_next_url($this->login("id"));
			\lib\debug::true(T_("your answer saved"));
			\lib\debug::msg($next_url);
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