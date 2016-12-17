-- multi_query
CREATE TRIGGER `transactionitems_count` AFTER INSERT ON `transactions` FOR EACH ROW BEGIN
UPDATE
	transactionitems
SET
	transactionitems.count = IF(transactionitems.count IS NULL OR transactionitems.count = '', 1, transactionitems.count + 1)
WHERE
	transactionitems.id = NEW.transactionitem_id;
END