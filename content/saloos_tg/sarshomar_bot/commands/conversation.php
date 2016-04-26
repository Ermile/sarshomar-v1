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
		$responde = null;
		switch ($_cmd['text'])
		{
			case 'سلام':
				$responde = 'سلام عزیزم';
				break;

			case 'خوبی':
				$responde = 'ممنون، خوبم';
				break;

			case 'چه خبرا':
				$responde = 'سلامتی';
				break;

			case 'حالت خوبه':
				$responde = 'عالی';
				break;

			case 'چاقی':
				$responde = 'نه!';
				break;

			case 'سلامتی':
				$responde = 'خدا رو شکر';
				break;

			case 'بمیر':
				$responde = 'مردن دست خداست';
				break;

			case 'بد':
				$responde = 'من بد نیستم';
				break;

			case 'خوب':
				$responde = 'ممنون';
				break;

			case 'زشت':
				$responde = 'من خوشگلم';
				break;

			case 'هوا گرمه':
				$responde = 'شاید!';
				break;

			case 'سردمه':
				$responde = 'بخاری رو روشن کن';
				break;

			case 'بد':
				$responde = 'من بد نیستم';
				break;

			case 'خر':
				$responde = 'خر خودتی'."\r\n";
				$responde .= 'باباته'."\r\n";
				$responde .= 'بی تربیت'."\r\n";
				break;

			case 'نفهم':
				$responde = 'من خیلی هم میفهمم';
				break;

			case 'خوابی':
				$responde = 'من همیشه بیدارم';
				break;

			case 'هی':
				$responde = 'بفرمایید';
				break;

			case 'الو':
				$responde = 'بله';
				break;

			case 'بلا':
				$responde = 'با ادب باش';
				break;

			default:
				$responde = false;
				break;
		}
		return $responde;
	}
}
?>