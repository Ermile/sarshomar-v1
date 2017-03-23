<?php
namespace content\saloos_tg\sarshomar_bot\commands\inline_query;
// use telegram class as bot
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\telegram\tg as bot;

trait gift
{
	public static function gift(&$result)
	{
		$result['results'][0] = [];
		$result['results'][0]['type'] = 'photo';
		$result['results'][0]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][0]['description'] = 'جایزه ورود به سرشمار تا ۲۲فروردین';
		$result['results'][0]['title'] = "آیفون ببرید";
		$result['results'][0]['url'] = "https://sarshomar.com/fa/gift";
		$result['results'][0]['id'] = "gift_fa";
		$result['results'][0]['caption'] = "با ورود به جامعه سرشمار علاوه بر دریافت هديه💵 ١٠٠هزار ريالى💵\nبپرسید، پاسخ دهید، درآمد کسب کنید و در قرعه کشی روز پدر\n آیفون ببرید📱😍";
		$result['results'][0]['photo_url'] = "https://sarshomar.com/static/images/gift/iphone-telegram.jpg";

	}
}
?>