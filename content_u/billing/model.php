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
		$meta            = [];
		$this->user_id   = $this->login('id');
		$meta['user_id'] = $this->user_id;
		$meta['unit']    = $this->view()->data->user_unit;

		$billing_history = \lib\db\transactions::search(null, $meta);
		return $billing_history;
	}

	/**
	 * post data and update or insert billing data
	 */
	public function post_billing()
	{
		if(!$this->login())
		{
			return debug::error(T_("You must login to pay amount"));
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
				// case '$1000$':
				// 	$amount = 1000;
				// 	break;

				// case '$2000$':
				// 	$amount = 2000;
				// 	break;

				// case '$$':
				// 	$amount = 100000;
				// 	break;
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
	 * Gets the verify.
	 *
	 * @return     <type>  The verify.
	 */
	public function get_verify()
	{
		$this->controller->display = false;
		$new_url = $this->view()->url->base;
		$new_url .= '/@/billing';
		debug::msg('direct', true);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::get(),
				'session' => $_SESSION,
			],
		];

		if(!$this->login())
		{
			\lib\db\logs::set('user:billing:verify:not:login', null, $log_meta);
			debug::error(T_("You must login to load this page"));
			$this->redirector($new_url);
			return;
		}

		$Authority = utility::get("Authority");
		$Status    = utility::get("Status");
		$log_caller = \lib\db\logitems::caller('payment:zarinpal:redirect');
		$log_where =
		[
			'logitem_id' => $log_caller,
			'log_data'   => $Authority,
			'user_id'    => $this->login('id'),
			'log_status' => 'enable',
		];
		$saved_log = \lib\db\logs::get($log_where);

		if(count($saved_log) === 1 && isset($saved_log[0]['id']))
		{
			// expire log
			\lib\db\logs::update(['log_status' => 'expire'], $saved_log[0]['id']);
		}
		elseif(!$saved_log || empty($saved_log))
		{
			\lib\db\logs::set('user:billing:verify:log:not:save', $this->login('id'), $log_meta);
			debug::error(T_("What happend?"));
			$this->redirector($new_url);
			return;
		}
		else
		{
			\lib\db\logs::set('user:billing:verify:log:error', $this->login('id'), $log_meta);
			debug::error(T_("What happend?, more than one zarinpal request!"));
			$this->redirector($new_url);
			return;
		}

		$url_bank = \lib\router::get_url(2);
		if(!in_array($url_bank, self::$support_bank))
		{

			\lib\db\logs::set('user:billing:verify:invalid:bank', $this->login('id'), $log_meta);
			\lib\error::page(T_("Invalid bank"));
		}

		if($url_bank == 'zarinpal')
		{
			$_SESSION['operation'] = false;
			$ok                    = true;

			if(utility::get('Authority') && utility::get('Status'))
			{
				$check_verify              = self::$zarinpal;
				$check_verify['Authority'] = $Authority;
				$check_verify['Status']    = $Status;

				if(isset($_SESSION['Amount']))
				{
					$check_verify['Amount']    = $_SESSION['Amount'];
				}
				else
				{
					\lib\db\logs::set('user:billing:verify:amount:not:found', $this->login('id'), $log_meta);
					debug::error(T_("Amount not found"));
					$ok = false;
				}

				if($ok)
				{
					\lib\utility\payment\zarinpal::$save_log = true;
					\lib\utility\payment\zarinpal::$user_id = $this->login('id');
					$check = payment\zarinpal::verify($check_verify);

					if($check && debug::$status)
					{
						\lib\db\logs::set('user:billing:verify:successful', $this->login('id'), $log_meta);
						\lib\db\transactions::set('real:charge:toman', $this->login('id'), ['plus' => $_SESSION['Amount']]);
						debug::true(T_("Payment operation was successfull and :amount :unit added to your cash", ['amount' => $_SESSION['Amount'], 'unit' => T_('toman')]));
						$_SESSION['operation'] = true;
					}
				}
			}
		}

		$this->redirector($new_url);
	}

}
?>