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
	 * PAYMENT DATA
	 *
	 * @var        array
	 */
	public static $PAYMENT_DATA = [];

	/**
	 * the user id
	 *
	 * @var        <type>
	 */
	public $user_id = null;

	use tools\zarinpal;
	use tools\payment;
	use tools\unit;


	/**
	 * get billing data to show
	 */
	public function get_billing($_args)
	{
		if(!$this->login())
		{
			return false;
		}
		$this->user_id = $this->login('id');
		$billing_history = \lib\db\transactions::search(null, ['user_id' => $this->user_id]);
		return $billing_history;
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

		$this->user_id = $this->login('id');

		if(!$this->user_unit())
		{
			return;
		}

		if(utility::post('bank'))
		{
			if(!in_array(mb_strtolower(utility::post('bank')), self::$support_bank))
			{
				return debug::error(T_("This gateway is not supported by Sarshomar"));
			}

			if(!utility::post('amount'))
			{
				return debug::error(T_("Amount not set"), 'amount', 'arguments');
			}

			return $this->pay();
		}

		if(utility::post('promo'))
		{
			$amount = 0;
			switch (utility::post('promo'))
			{
				case '$1000$':
					$amount = 1000;
					break;

				case '$2000$':
					$amount = 2000;
					break;

				case '$$':
					$amount = 100000;
					break;
				default:
					return debug::error(T_("Invalid promo code"), 'promo', 'arguments');
					break;
			}
			$this->save_transaction($amount);
			return debug::true(T_("Your account charge :amount", ['amount' => $amount]));
		}
		else
		{
			return debug::error(T_("Invalid promo code"), 'promo', 'arguments');
		}
	}



	/**
	 * pay amount
	 */
	public function pay()
	{
		\lib\db\logs::set('user:charge:real', $this->user_id);

		self::$zarinpal['Description'] = T_("Charge Sarshomar");

		$host  = Protocol."://" . \lib\router::get_root_domain();
		$lang = \lib\define::get_current_language_string();
		$host .= $lang;
		$host .= '/@/billing/verify/zarinpal';

		self::$zarinpal['CallbackURL'] = $host;

		if(mb_strtolower(utility::post('bank')) == 'zarinpal')
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
					debug::error(T_("Amount not found"));
					return false;
				}

				$check = payment\zarinpal::verify($check_verify);
				if($check && debug::$status)
				{
					if($this->save_transaction($_SESSION['Amount']))
					{
						return true;
					}
					return false;
				}
			}
		}
		return false;
		// $this->redirector()->set_url("@/billing")->redirect();
	}


	/**
	 * Saves a transaction.
	 */
	public function save_transaction($_amount, $_caller = 'real:charge:toman')
	{
		if(!$this->login())
		{
			return debug::errorT_("You must login to pay amount");
		}

		if(!$_amount)
		{
			return debug::errorT_("No amount was set");
		}
		\lib\db\transactions::set($_caller, $this->login('id'), ['plus' => $_amount]);
	}
}
?>