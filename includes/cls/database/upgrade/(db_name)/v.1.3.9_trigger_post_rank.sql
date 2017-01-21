-- multi_query
CREATE TRIGGER `ranks_change_posts_rank_on_update` AFTER UPDATE ON `ranks` FOR EACH ROW
BEGIN
	UPDATE posts SET posts.post_rank = NEW.value WHERE posts.id = NEW.post_id LIMIT 1;
END