-- multi_query
CREATE TRIGGER `transactions_change_transactionitems_count_on_insert` AFTER INSERT ON `transactions` FOR EACH ROW
BEGIN
	UPDATE
		transactionitems
	SET
		transactionitems.count = IF(transactionitems.count IS NULL OR transactionitems.count = '', 1, transactionitems.count + 1)
	WHERE
		transactionitems.id = NEW.transactionitem_id
	LIMIT 1;
END