<?php
namespace content_u\billing;
use \lib\utility;
use \lib\debug;
use \lib\utility\payment;

class model extends \mvc\model
{

	/**
	 * get billing data to show
	 */
	public function get_billing()
	{

	}


	/**
	 * post data and update or insert billing data
	 */
	public function post_billing()
	{
		$support_bank =
		[
			'zarinpal',
			'melli',
			'parsian',
			'mellat',
			'saman',
			'tejarat',
		];

		if(utility::post('bank'))
		{
			if(!in_array(strtolower(utility::post('bank')), $support_bank))
			{
				return debug::error(T_("This bank is not support by us"));
			}

			if(!utility::post('amount'))
			{
				return debug::error(T_("Amount not set"), 'amount', 'arguments');
			}

			return $this->pay();
		}

		if(utility::post('promo'))
		{
			return debug::error(T_("Invalid promo code"), 'promo', 'arguments');
		}
	}


	/**
	 * zarinpay method
	 *
	 * @var        array
	 */
	private static $zarinpal =
	[
		'MerchantID'  => "669de6b4-f744-11e6-9180-005056a205be",
		'Description' => "Sarshomar",
		'CallbackURL' => 'https://sarshomar.com/',
		'Email'       => null,
		'Mobile'      => null,
		'Amount'      => null,
	];

	/**
	 * pay amount
	 */
	public function pay()
	{
		if(strtolower(utility::post('bank')) == 'zarinpal')
		{
			self::$zarinpal['Amount'] = utility::post('amount');
			return payment\zarinpal::pay(self::$zarinpal);
		}
	}
}
?>