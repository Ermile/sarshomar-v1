<?php
namespace content\home;
use \lib\saloos;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		// route contact form
		if(\lib\router::get_url() == 'contact')
		{
			\lib\router::set_controller("\\content\\contact\\controller");
			return;
		}

		$reg = "/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/";

		if(preg_match($reg, \lib\router::get_url(), $controller_name))
		{
			if(isset($controller_name[4]) && $controller_name[4] == 'comments')
			{
				\lib\router::set_controller("\\content\\comments\\controller");
			}
			else
			{
				\lib\router::set_controller("\\content\\poll\\controller");
			}
			return;
		}

		$short_url = "/^\\$([". self::$shortURL. "]+)$/";
		if(preg_match($short_url, \lib\router::get_url()))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}


		if(preg_match("/^sp\_([". self::$shortURL. "]+)$/", \lib\router::get_url(), $split_url))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
		}

		if(substr(\lib\router::get_url(), 0, 1) == '$' && !$this->model()->s_template_finder())
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return;
		}

		/**
		 * generate captcha code
		 */
		if(\lib\router::get_url() == 'features/guest')
		{
			if(!$this->login())
			{
				$captcha_code = \lib\utility\captcha::creat();
				$this->view()->data->captcha = $captcha_code;
			}
		}

		if($this->model()->get_posts(false, null, ['check_language' => false, 'post_type' => ['poll', 'survey']]))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}

		$this->get("random")->ALL("/ask\/random$/");

		$this->get("ask")->ALL("/ask$/");

		$this->get("prev")->ALL("/ask\/prev$/");

	}
}
?>