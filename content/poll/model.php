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
		if(utility::post("poll_id"))
		{
			if(isset($_SESSION['last_poll_id']) && utility::post("poll_id") == $_SESSION['last_poll_id'])
			{
				$poll_id = $_SESSION['last_poll_id'];
			}
			else
			{
				\lib\debug::error(T_("poll id not match with your last question"));
				return false;
			}
		}
		else
		{
			\lib\debug::error(T_("poll id not found"));
			return false;
		}

		if(utility::post("type") == "bookmark")
		{
			if(isset($_SESSION['last_poll_id']) && utility::post("poll_id") == $_SESSION['last_poll_id'])
			{
				$args =
				[
					'poll_id' => utility::post("poll_id"),
					'user_id' => $this->login("id")
				];

				$result = \lib\db\polls::set_bookmark($args);

				if($result)
				{
					\lib\debug::true(T_("bookmark saved"));
				}
				else
				{
					\lib\debug::fatal(T_("error in save bookmark"));
				}
			}
		}
		else
		{

				// \lib\debug::true(T_("ysdfsdfsdfdsfdsfdsour answer saved"));
				// return true;
			$answer_key  = utility::post("answer_key");
			$answer_text = utility::post("answer_text");

			$check = false;
			if(isset($_SESSION['last_poll_opt']) && is_array($_SESSION['last_poll_opt']))
			{
				foreach ($_SESSION['last_poll_opt'] as $key => $value)
				{
					if((isset($value['key']) && $value['key'] == $answer_key) || $answer_key == "opt_0"  )
					{
						$check = true;
					}
				}
				// for descriptive mod
				if(isset($_SESSION['descriptive']) && $answer_key == "opt_". (count($_SESSION['last_poll_opt']) + 1) )
				{
					$check = true;
					$answer_text = utility::post("other_opt");
				}
			}
			if($check)
			{
				$result = \lib\db\answers::save($this->login('id'), $poll_id, $answer_key, $answer_text);
				if($result)
				{
					\lib\debug::true(T_("your answer saved"));
					\lib\debug::msg(\lib\db\polls::get_next_url($this->login("id")));
					return \lib\db\polls::get_next_url($this->login("id"));
				}
				else
				{
					\lib\debug::error(T_("error in save your answer"));
					return false;
				}

			}
			else
			{
				\lib\debug::error(T_("answer key not found"));
				\lib\debug::msg(\lib\db\polls::get_next_url($this->login("id")));
				// return false;
			}
		}
	}
}
?>