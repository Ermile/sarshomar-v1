<?php
namespace content_u\billing\tools;
use \lib\utility;
use \lib\debug;

trait payment
{

	/**
	 * { function_description }
	 *
	 * @param      <type>  $_bank  The bank
	 */
	public static function payment_data($_bank)
	{

		if(!isset(self::$PAYMENT_DATA[$_bank]))
		{
			$where =
			[
				'user_id'       => null,
				'post_id'       => null,
				'option_cat'    => 'payment_data',
				'option_key'    => $_bank,
				'option_status' => 'enable',
				'limit'			=> 1,
			];
			$result = \lib\db\options::get($where);
			if(isset($result['value']))
			{
				self::$PAYMENT_DATA[$_bank] = $result;
			}
		}

		if(isset(self::$PAYMENT_DATA[$_bank]))
		{
			return self::$PAYMENT_DATA[$_bank];
		}
		return [];
	}


	/**
	 * pay amount
	 */
	public function pay()
	{
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'session' => $_SESSION,
			],
		];

		self::$zarinpal['Description'] = T_("Charge Sarshomar");

		$host  = Protocol."://" . \lib\router::get_root_domain();
		$lang = \lib\define::get_current_language_string();
		$host .= $lang;
		$host .= '/@/billing/verify/zarinpal';

		self::$zarinpal['CallbackURL'] = $host;

		if(mb_strtolower(utility::post('bank')) == 'zarinpal')
		{
			$amount  = utility::post('amount');
			$log_meta['data'] = $amount;
			if(!is_numeric($amount) || intval($amount) < 1)
			{
				\lib\db\logs::set('user:billing:charge:error:invalid:amount', $this->user_id, $log_meta);
				debug::error(T_("Invalid amount"), 'amount', 'arguments');
				return false;
			}

			self::$zarinpal['Amount'] = $amount;
			$_SESSION['Amount']       = $amount;
			\lib\db\logs::set('user:billing:charge:zarinpal', $this->user_id, $log_meta);
			\lib\utility\payment\zarinpal::$save_log = true;
			\lib\utility\payment\zarinpal::$user_id  = $this->user_id;
			return \lib\utility\payment\zarinpal::pay(self::$zarinpal);
		}
	}


}
?>