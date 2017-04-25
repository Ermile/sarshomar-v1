<?php
namespace content\saloos_tg\sarshomarbot\commands\inline_query;
// use telegram class as bot
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\telegram\tg as bot;

trait gift
{
	public static function gift(&$result)
	{
		$result['results'][0] = [];
		$result['results'][0]['type'] = 'photo';
		$result['results'][0]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][0]['description'] = 'جایزه ورود به سرشمار تا نیمه شعبان';
		$result['results'][0]['title'] = "آیفون ببرید";
		$result['results'][0]['url'] = "https://sarshomar.com/fa/gift";
		$result['results'][0]['id'] = "gift_fa";
		$result['results'][0]['caption'] = "🎁 با ورود به سرشمار، در جشن بزرگ نیمه شعبان آیفون ببرید.\n\n💰+۱۰۰ هزار ریال اعتبار هدیه ثبت‌نام\n\n💡سرشمار؛ از هر کسی در هر مکانی بپرسید.\n@SarshomarBot\nSarshomar.com/fa";
		$result['results'][0]['photo_url'] = "https://sarshomar.com/static/images/gift/iphone-telegram.jpg";
		// $result['results'][0]['photo_url'] = "AgADBAAD4MA2G8keZAcXrq3tm_oHHdtfoBkABH8_esp20C2nTL0BAAEC";
		$result['results'][0]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> "ورود به بات",
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_11"
			]],
			[[
				"text" 	=> "وب‌سایت سرشمار",
				"url"	=> "https://sarshomar.com/fa"
			],
		]];
	}

	public static function gift2(&$result)
	{
		$result['results'][0] = [];
		$result['results'][0]['type'] = 'photo';
		$result['results'][0]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][0]['description'] = 'جایزه ورود به سرشمار تا ۲۲فروردین';
		$result['results'][0]['title'] = "آیفون ببرید";
		$result['results'][0]['url'] = "https://sarshomar.com/fa/gift";
		$result['results'][0]['id'] = "gift_fa_unoffical";
		$result['results'][0]['caption'] = "🎁 با سرشمار، امسال روز مرد، به جای جوراب، آیفون هدیه بگیرید!\n\n💰 +۱۰۰ هزار ریال هدیه ثبت‌نام\n\n💡با پاسخ به سوالات سرشمار، پول بگیرید\n@SarshomarBot\nSarshomar.com/fa";
		$result['results'][0]['photo_url'] = "https://sarshomar.com/static/images/gift/iphone-telegram.jpg";
		// $result['results'][0]['photo_url'] = "AgADBAAD4MA2G8keZAcXrq3tm_oHHdtfoBkABH8_esp20C2nTL0BAAEC";
		$result['results'][0]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> "ورود به بات",
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_11"
			]],
			[[
				"text" 	=> "وب‌سایت سرشمار",
				"url"	=> "https://sarshomar.com/fa"
			],
		]];
	}
}
?>