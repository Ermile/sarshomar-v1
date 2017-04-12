CREATE DATABASE `sarshomar_log` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
DROP TRIGGER IF EXISTS `logs_change_logitems_count_on_insert`;
RENAME TABLE sarshomar.logitems TO sarshomar_log.logitems;
RENAME TABLE sarshomar.logs TO sarshomar_log.logs;