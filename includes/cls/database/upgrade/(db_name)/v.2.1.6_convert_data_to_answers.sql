INSERT IGNORE INTO answers
(
`post_id`,
`user_id`,
`lastopt`,
`createdate`,
`date_modified`
)
SELECT
polldetails.post_id,
polldetails.user_id,
polldetails.opt,
polldetails.insertdate,
polldetails.date_modified
FROM
polldetails;