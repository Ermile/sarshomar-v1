<?php
namespace content_u\billing\tools;

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
}
?>