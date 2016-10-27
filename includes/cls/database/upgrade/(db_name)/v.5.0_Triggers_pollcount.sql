BEGIN
SET @USER_ID = (SELECT user_id FROM posts WHERE posts.id = new.post_id LIMIT 1);
SET @ME = NEW.user_id;
IF @USER_ID THEN
	IF (NEW.opt = 0) THEN
		UPDATE users SET users.peopleskipped = IF(users.peopleskipped IS NULL , 1, users.peopleskipped + 1) WHERE users.id = @USER_ID;
		UPDATE users SET users.pollskipped = IF(users.pollskipped IS NULL , 1, users.pollskipped + 1) WHERE users.id = @ME;
	ELSE
		UPDATE users SET users.pollanswer = IF(users.pollanswer IS NULL , 1, users.pollanswer + 1) WHERE users.id = @ME;
		UPDATE users SET users.peopleanswer = IF(users.peopleanswer IS NULL , 1, users.peopleanswer + 1) WHERE users.id = @USER_ID;
	END IF;
END IF;
END;