DROP TRIGGER IF EXISTS `set_refrrered`;
CREATE TRIGGER `set_refrrered` AFTER UPDATE ON `users` FOR EACH ROW
BEGIN
IF new.user_parent IS NOT NULL THEN
	IF old.user_status = "active" THEN
		UPDATE users SET users.userverified = IF(users.userverified IS NULL, 1, users.userverified + 1) WHERE users.id = new.user_parent;
	ELSE
		UPDATE users SET users.userreferred = IF(users.userreferred IS NULL, 1, users.userreferred + 1) WHERE users.id = new.user_parent;
	END IF;
END IF;
IF old.user_parent IS NOT NULL THEN
	IF old.user_status = "active" THEN
		UPDATE users SET users.userverified = IF(users.userverified IS NULL, 1, users.userverified + 1) WHERE users.id = old.user_parent;
	ELSE
		UPDATE users SET users.userreferred = IF(users.userreferred IS NULL, 1, users.userreferred + 1) WHERE users.id = old.user_parent;
	END IF;
END IF;
END;

DROP TRIGGER IF EXISTS `set_dashboard`;
CREATE TRIGGER `set_dashboard` AFTER INSERT ON `polldetails` FOR EACH ROW BEGIN
SET @USER_ID = (SELECT user_id FROM posts WHERE posts.id = new.post_id LIMIT 1);
SET @ME = new.user_id;
IF @USER_ID THEN
	IF (new.opt = 0) THEN
		UPDATE users SET users.peopleskipped = IF(users.peopleskipped IS NULL, 1, users.peopleskipped + 1) WHERE users.id = @USER_ID;
		UPDATE users SET users.pollskipped = IF(users.pollskipped IS NULL, 1, users.pollskipped + 1) WHERE users.id = @ME;
	ELSE
		UPDATE users SET users.pollanswer = IF(users.pollanswer IS NULL, 1, users.pollanswer + 1) WHERE users.id = @ME;
		UPDATE users SET users.peopleanswer = IF(users.peopleanswer IS NULL, 1, users.peopleanswer + 1) WHERE users.id = @USER_ID;
	END IF;
END IF;
END
