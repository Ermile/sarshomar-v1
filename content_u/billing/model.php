<?php
namespace content_u\billing;
use \lib\utility;
use \lib\debug;
use \lib\utility\payment;

class model extends \mvc\model
{

	public static $support_bank =
	[
		'zarinpal',
		// 'melli',
		// 'parsian',
		// 'mellat',
		// 'saman',
		// 'tejarat',
	];


	/**
	 * zarinpay method
	 *
	 * @var        array
	 */
	private static $zarinpal =
	[
		'MerchantID'  => "669de6b4-f744-11e6-9180-005056a205be",
		'Description' => "Sarshomar",
		'CallbackURL' => 'http://sarshomar.dev/@/billing/verify/zarinpal',
		'Email'       => null,
		'Mobile'      => null,
		'Amount'      => null,
	];


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
		if(!$this->login())
		{
			return debug::errorT_("You must login to pay amount");
		}

		if(utility::post('bank'))
		{
			if(!in_array(strtolower(utility::post('bank')), self::$support_bank))
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
	 * pay amount
	 */
	public function pay()
	{
		self::$zarinpal['Description'] = T_("Charge Sarshomar");
		if(strtolower(utility::post('bank')) == 'zarinpal')
		{
			$amount                   = utility::post('amount');
			self::$zarinpal['Amount'] = $amount;
			$_SESSION['Amount']       = $amount;
			return payment\zarinpal::pay(self::$zarinpal);
		}
	}


	/**
	 * Gets the verify.
	 *
	 * @return     <type>  The verify.
	 */
	public function get_verify()
	{
		$url_bank = \lib\router::get_url(2);
		if(!in_array($url_bank, self::$support_bank))
		{
			\lib\error::page("Invalid bank");
		}

		if($url_bank == 'zarinpal')
		{
			if(utility::get('Authority') && utility::get('Status'))
			{
				$check_verify              = self::$zarinpal;
				$check_verify['Authority'] = utility::get("Authority");
				$check_verify['Status']    = utility::get("Status");

				if(isset($_SESSION['Amount']))
				{
					$check_verify['Amount']    = $_SESSION['Amount'];
				}
				else
				{
					return debug::error(T_("Amount not found"));
				}

				$check = payment\zarinpal::verify($check_verify);
				if($check && debug::$status)
				{
					return $this->save_transaction($_SESSION['Amount']);
				}
			}
		}
		$this->redirector()->set_url("@/billing")->redirect();
	}


	/**
	 * Saves a transaction.
	 */
	public function save_transaction($_amount)
	{
		if(!$this->login())
		{
			return debug::errorT_("You must login to pay amount");
		}

		if(!$_amount)
		{
			return debug::errorT_("No amount was set");
		}

		$transaction = new \lib\utility\transaction;
		$save = $transaction->caller('charge:real')->user_id($this->login('id'))->save();
	}
}
?>