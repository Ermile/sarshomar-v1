<?php
namespace lib\utility;

class captcha
{

	/**
	 * create new captcha code
	 */
	public static function creat()
	{
		$random = rand(1000,9999);
		$_SESSION['captcha_code'] = $random;
		return $random;
	}


	/**
	 * check the $_captcha whit saved captcha
	 *
	 * @param      <type>  $_captcha  The captcha
	 */
	public static function check($_captcha)
	{
		if(isset($_SESSION['captcha_code']) && $_SESSION['captcha_code'] == $_captcha)
		{
			return true;
		}
		return false;
	}
}
?>