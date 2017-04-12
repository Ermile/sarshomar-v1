CREATE DATABASE IF NOT EXISTS `sarshomar_log` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE sarshomar_log.logitems (
  `id` smallint(5) UNSIGNED NOT NULL,
  `logitem_type` varchar(100) DEFAULT NULL,
  `logitem_caller` varchar(100) NOT NULL,
  `logitem_title` varchar(100) NOT NULL,
  `logitem_desc` varchar(100) DEFAULT NULL,
  `logitem_meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `logitem_priority` enum('critical','high','medium','low') NOT NULL DEFAULT 'medium',
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `logs`
--

CREATE TABLE sarshomar_log.logs (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logitem_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `log_data` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `log_desc` varchar(250) DEFAULT NULL,
  `log_meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `log_status` enum('enable','disable','expire','deliver') DEFAULT NULL,
  `log_createdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for table `logitems`
--
ALTER TABLE sarshomar_log.logitems
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE sarshomar_log.logs
  ADD PRIMARY KEY (`id`),
  ADD KEY `logs_logitems_id` (`logitem_id`) USING BTREE;

--
-- AUTO_INCREMENT for table `logitems`
--
ALTER TABLE sarshomar_log.logitems
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE sarshomar_log.logs
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `logs`
--
ALTER TABLE sarshomar_log.logs
  ADD CONSTRAINT `logs_logitems_id` FOREIGN KEY (`logitem_id`) REFERENCES `logitems` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT sarshomar_log.logitems SELECT * FROM sarshomar.logitems;

INSERT sarshomar_log.logs SELECT * FROM sarshomar.logs;