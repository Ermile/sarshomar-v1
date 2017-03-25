<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_transactions
{
	private static function get_transactions(&$_poll_data)
	{
		$user_id = self::$private_user_id;
		$post_id = self::$private_poll_id;
		$query =
		"
			SELECT transactions.*, units.title AS `unit_title`
			FROM transactions
			INNER JOIN transactionitems ON transactionitems.id = transactions.transactionitem_id
			INNER JOIN units ON transactions.unit_id = units.id
			WHERE
				transactions.user_id = $user_id AND
				transactions.post_id = $post_id AND
				transactionitems.caller LIKE '%:answer:poll%'
		";
		$prize = \lib\db::get($query);

		if(count($prize) === 1)
		{
			$_poll_data['my_prize'] = [];

			if(isset($prize[0]['plus']))
			{
				$_poll_data['my_prize']['value'] = $prize[0]['plus'];
			}

			if(isset($prize[0]['unit_title']))
			{
				$_poll_data['my_prize']['unit'] = $prize[0]['unit_title'];
			}
		}
	}
}
?>