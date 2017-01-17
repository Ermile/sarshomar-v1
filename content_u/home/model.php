<?php
namespace content_u\home;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * Posts a captcha.
	 */
	public function post_captcha()
	{
		if(utility::post("ui-language"))
		{
			return $this->set_ui_language();
		}

		$captcha = utility::post("captcha");
		if(utility\captcha::check($captcha))
		{
			$signup_inspection = \lib\db\users::signup_inspection();
			if($signup_inspection)
			{
				\lib\db\users::set_login_session(null, null, $signup_inspection);
				$this->redirector($this->url("base"). "/@");
			}
		}
	}

	public function set_ui_language()
	{

		$lang = utility::post("ui-language");
		if(!$this->login())
		{
			return debug::error(T_("Please login to set language"));
		}

		if(\lib\utility\location\languages::check($lang))
		{
			\lib\db\users::set_language($lang, ['user_id' => $this->login("id")]);
			if(\lib\define::get_language() != $lang)
			{
				if(\lib\define::get_language("default") != $lang)
				{
					$url = $this->url('root'). "/$lang/". $this->url('content');
				}
				else
				{
					$url = $this->url('root'). "/". $this->url('content');
				}
				$this->redirector($url);
				\lib\debug::msg('direct', true);

			}
		}
		else
		{
			return debug::error(T_("Invalid paramert language"), 'ui-language');
		}

	}

}
?>