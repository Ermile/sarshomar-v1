CREATE DATABASE `sarshomar_log` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
DROP TRIGGER IF EXISTS `logs_change_logitems_count_on_insert`;
CREATE TABLE sarshomar_log.logs AS SELECT * FROM sarshomar.logs;
CREATE TABLE sarshomar_log.logitems AS SELECT * FROM sarshomar.logitems;