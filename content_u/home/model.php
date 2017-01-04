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

}
?>