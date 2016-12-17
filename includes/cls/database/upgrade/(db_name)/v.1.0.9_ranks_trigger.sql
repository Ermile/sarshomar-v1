-- multi_query
CREATE TRIGGER `set_post_ranks` AFTER UPDATE ON `ranks` FOR EACH ROW BEGIN
UPDATE posts SET posts.post_rank = NEW.value WHERE posts.id = NEW.post_id;
END