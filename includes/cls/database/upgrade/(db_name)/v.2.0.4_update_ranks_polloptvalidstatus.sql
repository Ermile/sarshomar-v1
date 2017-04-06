UPDATE ranks set ranks.vote =
(
	SELECT IFNULL(SUM(pollstats.total), 0) FROM pollstats WHERE pollstats.post_id = ranks.post_id
);

UPDATE polldetails SET polldetails.validstatus =
(
	SELECT
		IF(users.user_verify IS NULL OR users.user_verify = 'unknown', NULL, IF(users.user_verify = 'uniqueid', 'invalid', 'valid'))
	FROM users
	WHERE users.id = polldetails.user_id
)
WHERE polldetails.validstatus IS NULL;