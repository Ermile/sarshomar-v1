<?php

// self::$perm_list[] =
// [
// 	'caller'      => 'add:company',
// 	'title'       => T_("Add new company"),
// 	'desc'        => T_("Add new company"),
// 	'group'       => 'plan_1',
// 	'need_check'  => true,
// 	'need_verify' => true,
// 	'enable'      => true,
// ];



self::$perm_list[1] =
[
	'caller' => 'admin',
];
self::$perm_list[2] =
[
	'caller' => 'admin:everyone_login:view',
];
self::$perm_list[3] =
[
	'caller' => 'admin:admin',
];
self::$perm_list[4] =
[
	'caller' => 'admin:admin:admin',
];
self::$perm_list[5] =
[
	'caller' => 'election:admin:admin',
];
self::$perm_list[6] =
[
	'caller' => 'election:data:admin',
];
self::$perm_list[7] =
[
	'caller' => 'u:upload_1000_mb:view',
];
self::$perm_list[8] =
[
	'caller' => 'u:upload_100_mb:view',
];
self::$perm_list[9] =
[
	'caller' => 'u:upload_10_mb:view',
];
self::$perm_list[10] =
[
	'caller' => 'u:delete_account:view',
];
self::$perm_list[11] =
[
	'caller' => 'u:complete_profile:admin',
];
self::$perm_list[12] =
[
	'caller' => 'u:free_add_poll:view',
];
self::$perm_list[13] =
[
	'caller' => 'u:free_account:view',
];
self::$perm_list[14] =
[
	'caller' => 'u:sarshomar:view',
];
self::$perm_list[15] =
[
	'caller' => 'u:free_add_filter:view',
];
self::$perm_list[16] =
[
	'caller' => 'admin:admin:view',
];
self::$perm_list[17] =
[
	'caller' => 'admin:admin',
];

?>