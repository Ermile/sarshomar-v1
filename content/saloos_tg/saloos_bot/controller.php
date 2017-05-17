<?php
namespace content\saloos_tg\saloos_bot;
// use telegram class as bot
use \lib\telegram\tg as bot;

class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/saloos_bot/'.\lib\option::social('telegram', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key   = '164997863:AAFC3nUcujDzpGq-9ZgzAbZKbCJpnd0FWFY';
			bot::$name      = 'saloos_bot';
			bot::$botan     = 'Tlnch-gJYsB7kyLoIPlQG0S9LpJdviKu';
			// bot::$cmdFolder = '\\'. __NAMESPACE__ .'\commands\\';
			// bot::$useSample = true;
			bot::$defaultText = 'تعریف نشده';

			$fullName = 'هتل بین المللی آرامیس تهران';

			// add about text
			$txt_about      = "هتل بوتیک تجاری آرامیس با ۸۴ واحد اقامتی شامل اتاق و سوئیت مجلل و مدرن پذیرای مهمانان عزیز می باشد.\r\n";
			$txt_about      .= "آرامش و آسایش به همراه لذیذترین غذاهای محلی گیلان را در رستوران مجلل چاچوق تجربه کنید.\r\n\n";
			// $txt_about      .= "این رستوران در یک طبقه مجزا و به ظرفیت ۴۵۰ نفر طراحی شده است.";

			// add contact text
			$txt_contact    = "تلفن : 88933402-021\r\n";
			$txt_contact    .= "ایمیل :‌ info@aramis-hotel.com\r\n";
			$txt_contact    .= "نشانی: تهران، خیابان ولیعصر، بالاتر از میدان ولیعصر، حد فاصل سینما استقلال و آفریقا، پلاک 1752\r\n".$fullName;

			$txt_general     = "مشخصات عمومی $fullName\r\n";
			$txt_general     .= "- تعداد اتاق ها: ۸۴ اتاق\r\n";
			$txt_general     .= "- تعداد طبقات: ۶ طبقه\r\n";
			$txt_general     .= "- تعداد تخت ها: ۱۷۶ تخت\r\n";
			$txt_general     .= "- ظرفیت لابی با ظرفیت ۱۵ نفر\r\n";
			$txt_general     .= "- وضعیت ترافیک محدوده طرح ترافیک\r\n-";

			$txt_feature  =  "امکانات $fullName\r\n";
			$txt_feature  .= "- آسانسور\n";
			$txt_feature  .= "- صبحانه\n";
			$txt_feature  .= "- قفل درب کارتی\n";
			$txt_feature  .= "- لابی\n";
			$txt_feature  .= "- اینترنت در لابی\n";
			$txt_feature  .= "- پذیرش ۲۴ ساعته\n";
			$txt_feature  .= "- صندوق امانات\n-";

			bot::$fill    =
			[
				'name'     => 'ارمایل',
				'fullName' => $fullName,
				'about'    => $txt_about,
				'contact'  => $txt_contact,
				'global'   => $txt_general,
				'feature'  => $txt_feature,
				'intro'    => "معرفی",
				'list'     => "$fullName\nلیست سوییت‌ها به شرح زیر است",
				'type'     => 'هتل',
			];
			$result         = bot::run();

			if(\lib\option::social('telegram', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>