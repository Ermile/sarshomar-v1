<?php
namespace content_u\home;

class model extends \mvc\model
{

	/**
	 * Posts a captcha.
	 */
	public function post_captcha()
	{
		$captcha = \lib\utility::post("captcha");
		if(\lib\utility\captcha::check($captcha))
		{
			$signup_inspection = \lib\db\users::signup_inspection();
			if($signup_inspection)
			{
				\lib\db\users::set_login_session(null, null, $signup_inspection);
				$this->redirector($this->url("base"). "/@");
			}
		}
	}


	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url($_args, $_type = "decode")
	{
		if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
		{
			if(!isset($_args->match->url[0][1]))
			{
				return false;
			}

			$url     = $_args->match->url[0][1];
			$poll_id = \lib\utility\shortURL::decode($url);

			// check is my poll this id
			if(!\lib\db\polls::is_my_poll($poll_id, $this->login('id')) && !$this->access('u', 'sarshomar_knowledge', 'admin'))
			{
				\lib\error::bad(T_("This is not your poll"));
				return false;
			}

			if($_type == "decode")
			{
				return $poll_id;
			}
			else
			{
				return $url;
			}
		}
		else
		{
			// \lib\debug::error(T_("Poll id not found"));
			return false;
		}
	}
}
?>