INSERT INTO userdashboards
(`user_id`,
`poll_answered`,
`poll_skipped`,
`survey_answered`,
`survey_skipped`,
`my_poll`,
`my_survey`,
`my_poll_answered`,
`my_poll_skipped`,
`my_survey_answered`,
`my_survey_skipped`,
`user_referred`,
`user_verified`,
`comment_count`,
`draft_count`,
`publish_count`,
`awaiting_count`,
`my_fav`,
`my_like`
)

SELECT users.id,

(SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = users.id AND
	polldetails.type  = 'poll' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0
	) AS `poll_answered` ,
(
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = users.id AND
	polldetails.type  = 'poll' AND
	polldetails.status  = 'enable' AND
	polldetails.opt   = 0
	) as `poll_skipped` ,

  (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = users.id AND
	polldetails.type  = 'survey' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0
	)  as `survey_answered`,
  (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = users.id AND
	polldetails.type  = 'survey' AND
	polldetails.status  = 'enable' AND
	polldetails.opt   = 0
	) as `survey_skipped`,
  (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id = users.id AND
	posts.post_type = 'poll' AND
	posts.post_status IN ('daraft', 'publish', 'awaiting','trash','pause', 'stop')
	) as `my_poll` ,
	  (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id = users.id AND
	posts.post_type = 'survey' AND
	posts.post_status IN ('daraft', 'publish', 'awaiting','trash','pause', 'stop')
	) as `my_survey` ,
	  (
SELECT
	COUNT(polldetails.id)
FROM
	polldetails
INNER JOIN posts
	ON polldetails.post_id = posts.id AND posts.post_type = 'poll'
WHERE
	polldetails.type  = 'poll' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0 AND
	polldetails.post_id IN
		(
	SELECT posts.id
	FROM posts
	WHERE
		posts.user_id = users.id AND
		posts.post_type = 'poll'
		)
	) as `my_poll_answered` ,
	  (
SELECT
	COUNT(polldetails.id)
FROM
	polldetails
INNER JOIN posts
	ON polldetails.post_id = posts.id AND posts.post_type = 'poll'
WHERE
	polldetails.type = 'poll' AND
	polldetails.status = 'enable' AND
	polldetails.opt  = 0 AND
	polldetails.post_id IN
		(
	SELECT posts.id
	FROM posts
	WHERE
		posts.user_id = users.id AND
		posts.post_type = 'poll'
		)
	) as `my_poll_skipped` ,
	  (
SELECT
	COUNT(polldetails.id)
FROM
	polldetails
INNER JOIN posts
	ON polldetails.post_id = posts.id AND posts.post_type = 'survey'
WHERE
	polldetails.type  = 'survey' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0 AND
	polldetails.post_id IN
		(
	SELECT posts.id
	FROM posts
	WHERE
		posts.user_id = users.id AND
		posts.post_type = 'survey'
		)
	) as `my_survey_answered` ,
	  (
SELECT
	COUNT(polldetails.id)
FROM
	polldetails
INNER JOIN posts
	ON polldetails.post_id = posts.id AND posts.post_type = 'survey'
WHERE
	polldetails.type = 'survey' AND
	polldetails.status = 'enable' AND
	polldetails.opt  = 0 AND
	polldetails.post_id IN
		(
	SELECT posts.id
	FROM posts
	WHERE
		posts.user_id = users.id AND
		posts.post_type = 'survey'
		)
	) as `my_survey_skipped` ,
	  (
SELECT
	COUNT(users.id)
FROM
	users
WHERE
	users.user_parent = users.id
	) as `user_referred` ,
	  (
SELECT
	COUNT(users.id)
FROM
	users
WHERE
	users.user_parent = users.id AND
	users.user_status = 'active'
	) as `user_verified` ,
	  (
SELECT COUNT(comments.id) FROM comments WHERE comments.user_id = users.id
	) as `comment_count` ,
	  (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = users.id AND
	posts.post_status = 'daraft'
	) as `draft_count` ,
	  (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = users.id AND
	posts.post_status = 'publish'
	) as `publish_count` ,
	  (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = users.id AND
	posts.post_status = 'awaiting'
	) as `awaiting_count` ,
(
SELECT COUNT(options.id)
FROM options
WHERE
	options.user_id       = users.id AND
	options.option_cat    = CONCAT('user_detail_', users.id) AND
	options.option_key    = 'fav' AND
	options.option_status = 'enable'
	) as `my_fav`
,
(SELECT COUNT(options.id)
FROM options
WHERE
	options.user_id       = users.id AND
	options.option_cat    = CONCAT('user_detail_', users.id) AND
	options.option_key    = 'like' AND
	options.option_status = 'enable'
	) as `my_like`

FROM users
