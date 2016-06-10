<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class product
{
	private static $products =
	[
		// pizza list
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'ایتالیایی',
			'price'	=> 15000,
			'desc'	=> 'خمیرسبک، سس مخصوص سرآشپز، بیکن مرغ و گوشت، پنیر پیتزا، قارچ ، فلفل دلمه ای',
		],
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'یونانی',
			'price'	=> 18000,
			'desc'	=> 'خمیر، سس مخصوص سرآشپز، فیله  مرغ  کباب شده، ژامبون مرغ، پنیر تست، پنیر پیتزا، فلفل دلمه ای، قارچ',
		],
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'سرآشپز',
			'price'	=> 13000,
			'desc'	=> 'خمیر، سس مخصوص سرآشپز، ژامبون پیتزایی، ژامبون مرغ و گوشت، پنیر تست، پنیر پیتزا، فلفل دلمه ای، قارچ، گوجه حبه ای',
		],
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'پپرونی',
			'price'	=> 12000,
			'desc'	=> 'خمیر، سس مخصوص سرآشپز، پپرونی،پنیر پیتزا، قارچ ',
		],
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'استثنایی',
			'price'	=> 25000,
			'desc'	=> 'خمیر، سس مخصوص سرآشپز، گوشت کلزون، فیله کباب شده مرغ، ژامبون مرغ، پنیرتست، خمیر سبک، پنیر پیتزا، قارچ، فلفل دلمه ای',
		],
		[
			'cat'	=> 'پیتزا',
			'name'	=> 'سبزیجات',
			'price'	=> 14000,
			'desc'	=> 'خمیر، سس مخصوص ، پنیر پیتزا، فلفل دلمه ای، نخودفرنگی، گوجه، ذرت  زیتون، قارچ با ادویه و سبزیجات معطر',
		],


		// Sandwich
		[
			'cat'	=> 'ساندویچ',
			'name'	=> 'چیزبرگر',
			'price'	=> 7000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'ساندویچ',
			'name'	=> 'همبرگر',
			'price'	=> 6000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'ساندویچ',
			'name'	=> 'چیپس و پنیر',
			'price'	=> 5000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'ساندویچ',
			'name'	=> 'چیزبرگر',
			'price'	=> 4000,
			'desc'	=> '',
		],


		// drink
		[
			'cat'	=> 'نوشیدنی',
			'name'	=> 'آب',
			'price'	=> 500,
			'desc'	=> '',
		],
		[
			'cat'	=> 'نوشیدنی',
			'name'	=> 'نوشابه',
			'price'	=> 1000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'نوشیدنی',
			'name'	=> 'دلستر',
			'price'	=> 2000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'نوشیدنی',
			'name'	=> 'آبمیوه',
			'price'	=> 3000,
			'desc'	=> '',
		],


		// Other
		[
			'cat'	=> 'پیش‌غذا',
			'name'	=> 'سالاد فصل',
			'price'	=> 2000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'پیش‌غذا',
			'name'	=> 'سالاد اندونزی',
			'price'	=> 3000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'پیش‌غذا',
			'name'	=> 'قارچ سوخاری',
			'price'	=> 4000,
			'desc'	=> '',
		],
		[
			'cat'	=> 'پیش‌غذا',
			'name'	=> 'سیب زمینی',
			'price'	=> 5000,
			'desc'	=> '',
		],
	];


	public static function get($_needle, $_arg = false)
	{
		if($_needle === 'catList' || $_needle === 'cat')
		{
			return self::catList();
		}
		elseif(is_string($_needle))
		{
			return self::productList($_needle, $_arg);
		}

		return false;
	}

	/**
	 * return list of category
	 * @return [type] [description]
	 */
	public static function catList()
	{
		$cats = array_column(self::$products, 'cat');
		$cats = array_unique($cats);
		return $cats;
	}


	/**
	 * return list of products
	 * @param  [type]  $_category [description]
	 * @param  boolean $_detail   [description]
	 * @return [type]             [description]
	 */
	public static function productList($_category = null, $_detail = false)
	{
		$products = [];
		foreach (self::$products as $key => $value)
		{
			if($_category === $value['cat'] || $_category === null)
			{
				if($_detail)
				{
					$products[] = $value;
				}
				else
				{
					$products[] = $value['name'];
				}
			}
		}

		return $products;
	}


	public static function detail($_productName, $_existCheck = false)
	{
		$product = null;
		foreach (self::$products as $key => $value)
		{
			if($_productName === $value['name'])
			{
				$product = $value;
			}
		}

		// if want to check product exist or not
		if($_existCheck)
		{
			if(is_array($product))
			{
				$product = true;
			}
			else
			{
				$product = false;
			}
		}

		return $products;
	}
}
?>