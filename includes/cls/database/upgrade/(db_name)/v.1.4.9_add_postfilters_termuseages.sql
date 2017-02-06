ALTER TABLE `termusages` CHANGE `termusage_foreign` `termusage_foreign`
ENUM('posts','products','attachments','files','comments','users','pollopts', 'profile', 'filter', 'cat', 'tag')
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;