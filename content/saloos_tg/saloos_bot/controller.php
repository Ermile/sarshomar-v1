<?php
namespace content\saloos_tg\saloos_bot;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/saloos_bot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key   = '164997863:AAFC3nUcujDzpGq-9ZgzAbZKbCJpnd0FWFY';
			// bot::$cmdFolder = '\\'. __NAMESPACE__ .'\commands\\';
			// bot::$useSample = true;
			bot::$defaultText = 'تعریف نشده';


			// add about text
			$txt_about      = "هتل بوتیک تجاری آرامیس با ۸۴ واحد اقامتی شامل اتاق و سوئیت مجلل و مدرن پذیرای مهمانان عزیز می باشد.\r\n";
			$txt_about      .= "آرامش و آسایش به همراه لذیذترین غذاهای محلی گیلان در رستوران مجلل چاچوق تجربه کنید.\r\n\n";
			$txt_about      .= "این رستوران در یک طبقه مجزا و به ظرفیت ۴۵۰ نفر طراحی شده است.";

			// add contact text
			$txt_contact    = "تلفن : 88933402-021\r\n";
			$txt_contact    .= "ایمیل :‌ info@aramis-hotel.com\r\n";
			$txt_contact    .= "ایمیل :‌ info@aramis-hotel.com\r\n";
			$txt_contact    .= "نشانی\r\n";
			$txt_contact    .= "تهران، خیابان ولیعصر، بالاتر از میدان ولیعصر، حد فاصل سینما استقلال و آفریقا، پلاک 1752\r\n";



			bot::$fill      =
			[
				'name'     => 'ارمایل',
				'fullName' => 'هتل بین المللی آرامیس تهران',
				'about'    => $txt_about,
				'contact'  => $txt_contact,
				'type'     => 'هتل',
			];
			$result         = bot::run();

			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>