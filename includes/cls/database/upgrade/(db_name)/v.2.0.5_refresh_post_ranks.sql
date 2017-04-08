UPDATE ranks set ranks.vote =
(
	SELECT IFNULL(SUM(pollstats.total), 0) FROM pollstats WHERE pollstats.post_id = ranks.post_id
);