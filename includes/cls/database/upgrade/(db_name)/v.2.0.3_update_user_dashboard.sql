UPDATE userdashboards
SET
userdashboards.user_id = userdashboards.user_id,

userdashboards.poll_answered = (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = userdashboards.user_id AND
	polldetails.type  = 'poll' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0
	) ,

userdashboards.poll_skipped = (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = userdashboards.user_id AND
	polldetails.type  = 'poll' AND
	polldetails.status  = 'enable' AND
	polldetails.opt   = 0
	) ,

userdashboards.survey_answered = (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = userdashboards.user_id AND
	polldetails.type  = 'survey' AND
	polldetails.status  = 'enable' AND
	polldetails.opt <> 0
	) ,

userdashboards.survey_skipped = (
SELECT COUNT(polldetails.id)
FROM polldetails
WHERE
	polldetails.user_id = userdashboards.user_id AND
	polldetails.type  = 'survey' AND
	polldetails.status  = 'enable' AND
	polldetails.opt   = 0
	) ,

userdashboards.my_poll = (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id = userdashboards.user_id AND
	posts.post_type = 'poll' AND
	posts.post_status IN ('draft', 'publish', 'awaiting','trash','pause', 'stop')
	) ,

userdashboards.my_survey = (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id = userdashboards.user_id AND
	posts.post_type = 'survey' AND
	posts.post_status IN ('draft', 'publish', 'awaiting','trash','pause', 'stop')
	) ,

userdashboards.my_poll_answered = (
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
		posts.user_id = userdashboards.user_id AND
		posts.post_type = 'poll'
		)
	) ,

userdashboards.my_poll_skipped = (
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
		posts.user_id = userdashboards.user_id AND
		posts.post_type = 'poll'
		)
	) ,

userdashboards.my_survey_answered = (
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
		posts.user_id = userdashboards.user_id AND
		posts.post_type = 'survey'
		)
	) ,

userdashboards.my_survey_skipped = (
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
		posts.user_id = userdashboards.user_id AND
		posts.post_type = 'survey'
		)
	) ,

userdashboards.user_referred = (
SELECT
	COUNT(users.id)
FROM
	users
WHERE
	users.user_parent = userdashboards.user_id
	) ,

userdashboards.user_verified = (
SELECT
	COUNT(users.id)
FROM
	users
WHERE
	users.user_parent = userdashboards.user_id AND
	users.user_status = 'active'
	) ,

userdashboards.comment_count = (
SELECT COUNT(comments.id) FROM comments WHERE comments.user_id = userdashboards.user_id
	) ,
userdashboards.draft_count = (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = userdashboards.user_id AND
	posts.post_status = 'draft'
	) ,

userdashboards.publish_count = (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = userdashboards.user_id AND
	posts.post_status = 'publish'
	) ,

userdashboards.awaiting_count = (
SELECT COUNT(posts.id)
FROM posts
WHERE
	posts.user_id   = userdashboards.user_id AND
	posts.post_status = 'awaiting'
	)