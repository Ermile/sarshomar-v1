UPDATE ranks set ranks.vote = (SELECT IFNULL(SUM(pollstats.total), 0) FROM pollstats WHERE pollstats.post_id = ranks.post_id);
UPDATE ranks set ranks.skip =
(
	SELECT 	IFNULL(COUNT(polldetails.id), 0)
	FROM polldetails
	WHERE polldetails.post_id = ranks.post_id
	AND polldetails.opt = 0
	AND polldetails.status = 'enable'
	AND polldetails.validstatus IS NOT NULL
);