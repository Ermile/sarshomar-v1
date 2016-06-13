<?php
namespace content\saloos_tg\germile_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu_food
{
	/**
	 * order
	 * @return [type] [description]
	 */
	public static function main()
	{
		$menu =
		[
			'keyboard' =>
			[
				["ساندویچ", "پیتزا"],
				["مخلفات", "نوشیدنی"],
				// ["بازگشت به منوی اصلی"]
			],
		];

		$result['text']         = "لطفا یکی از دسته‌بندی‌ها زیر را انتخاب کنید\n\n";
		$result['text']         .= "/cancel انصراف از ثبت سفارش ";
		$result['reply_markup'] = $menu;

		return $result;
	}
}
?>