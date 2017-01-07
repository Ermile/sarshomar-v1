ALTER TABLE `pollopts` ADD `type` ENUM('select','emoji','descriptive','upload','range','notification') NOT NULL AFTER `key`;
ALTER TABLE `pollopts` CHANGE `text` `title` varchar(700) NULL AFTER `post_id`;
