<?php
namespace content\home;
use \lib\saloos;

class controller extends \content\main\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		parent::_route();

		$site_url = \lib\router::get_url();

		switch ($site_url)
		{
			case 'image':
			case 'video':
			case 'audio':
				// redirect to homepage
				$this->redirector('/')->redirect();
				break;


			case 'ref':
				\lib\router::set_controller("\\content\\referer\\controller");
				return;
				break;

			case 'contact':
				// route contact form
				\lib\router::set_controller("\\content\\contact\\controller");
				return;
				break;

			default:
				break;
		}

		$reg = "/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/";

		if(preg_match($reg, $site_url, $controller_name))
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
		if(preg_match($short_url, $site_url))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}


		if(preg_match("/^sp\_([". self::$shortURL. "]+)$/", $site_url, $split_url))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
		}

		if(substr($site_url, 0, 1) == '$' && !$this->model()->s_template_finder())
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return;
		}

		/**
		 * generate captcha code
		 * features/guest
		 */
		if($site_url == 'benefits/guest')
		{
			if(!$this->login())
			{
				$captcha_code = \lib\utility\captcha::creat();
				$this->view()->data->captcha = $captcha_code;
			}
		}
		$check_status = $this->access('admin','admin', 'view') ? false : true ;
		$load_poll =
		[
			'post_status'    => self::$accept_poll_status,
			'check_status'   => $check_status,
			'check_language' => false,
			'post_type'      => ['poll', 'survey']
		];
		if($this->model()->get_posts(false, null, $load_poll))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}

		$this->get("random")->ALL("/ask\/random$/");

		$this->get("ask")->ALL("/ask$/");
		$this->get("next")->ALL("/next$/");
		$this->get("prev")->ALL("/prev$/");

	}
}
?>