<?php
namespace lib\db\transactions;
use \lib\db;
use \lib\utility;


trait get
{
	public static function get($_args)
	{
		$default_args =
		[
			'user_id'    => null,
			'start_date' => null,
			'end_date'   => null,
		];
		$_args = array_merge($default_args, $_args);
		if(!$_args['user_id'])
		{
			return debug::error(T_("User id not set"), 'user_id', 'db');
		}

		$query =
		"
			SELECT SQL_CALC_FOUND_ROWS
				transactions.title              AS `title`,
				-- transactions.transactionitem_id AS `transactionitem_id`,
				-- transactions.user_id            AS `user_id`,
				transactions.type               AS `type`,
				units.title            			AS `unit`,
				transactions.plus               AS `plus`,
				transactions.minus              AS `minus`,
				transactions.budgetbefore       AS `budgetbefore`,
				transactions.budget             AS `budget`,
				-- transactions.exchange_id        AS `exchange_id`,
				-- transactions.status             AS `status`,
				-- transactions.meta               AS `meta`,
				-- transactions.desc               AS `desc`,
				-- transactions.related_user_id    AS `related_user_id`,
				-- transactions.parent_id          AS `parent_id`,
				-- transactions.finished           AS `finished`
				transactions.createdate         AS `date`
			FROM
				transactions
			INNER JOIN units ON units.id = transactions.unit_id
			WHERE transactions.user_id = $_args[user_id]
		";

		$result = \lib\db::get($query);

		// $found_rows = \lib\db::get("SELECT FOUND_ROWS() AS `total`", 'total', true);
		// list($limit_start, $limit) = \lib\db::pagnation($found_rows, $limit);
		// $limit = " LIMIT $limit_start, $limit ";
		return $result;
	}
}
?>