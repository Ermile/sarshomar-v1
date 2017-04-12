CREATE TABLE sarshomar_log.apilogs (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `url` varchar(760) CHARACTER SET utf8mb4 DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `responseheader` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `requestheader` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `pagestatus` varchar(50) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `debug` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `apikey` varchar(200) DEFAULT NULL,
  `apikeyuserid` int(10) UNSIGNED DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `meta` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `clientip` int(50) UNSIGNED DEFAULT NULL,
  `createdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datemodified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Indexes for table `apilogs`
--
ALTER TABLE sarshomar_log.apilogs
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `apilogs`
--
ALTER TABLE sarshomar_log.apilogs
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT sarshomar_log.apilogs SELECT * FROM sarshomar.apilogs;
