<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class conversation extends \content\saloos_tg\sarshomar_bot\controller
{
	/**
	 * give input message and create best response for it
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function fa($_cmd)
	{
		$response = null;
		$text     = null;

		switch ($_cmd['text'])
		{
			case 'سلام':
				$text = 'سلام عزیزم';
				break;

			case 'خوبی':
				$text = 'ممنون، خوبم';
				break;

			case 'چه خبرا':
				$text = 'سلامتی';
				break;

			case 'حالت خوبه':
				$text = 'عالی';
				break;

			case 'چاقی':
				$text = 'نه!';
				break;

			case 'سلامتی':
				$text = 'خدا رو شکر';
				break;

			case 'بمیر':
				$text = 'مردن دست خداست';
				break;

			case 'بد':
				$text = 'من بد نیستم';
				break;

			case 'خوب':
				$text = 'ممنون';
				break;

			case 'زشت':
				$text = 'من خوشگلم';
				break;

			case 'هوا گرمه':
				$text = 'شاید!';
				break;

			case 'سردمه':
				$text = 'بخاری رو روشن کن';
				break;

			case 'بد':
				$text = 'من بد نیستم';
				break;

			case 'خر':
				$text = 'خر خودتی'."\r\n";
				$text .= 'باباته'."\r\n";
				$text .= 'بی تربیت'."\r\n";
				break;

			case 'نفهم':
				$text = 'من خیلی هم میفهمم';
				break;

			case 'خوابی':
				$text = 'من همیشه بیدارم';
				break;

			case 'هی':
				$text = 'بفرمایید';
				break;

			case 'الو':
				$text = 'بله';
				break;

			case 'بلا':
				$text = 'با ادب باش';
				break;

			default:
				$text = false;
				break;
		}
		// create response format
		if($text)
		{
			$response =
			[
				'text' => $text
			];
		}
		// return response as result
		return $response;
	}
}
?>