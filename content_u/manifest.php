<?php
$modules = array();

/**
 * poll to complete profile
 */
$modules['complete_profile'] = array(
	'desc' 			=> T_("Allow to add the poll for profile completion"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);


/**
 *	sarshomart knowledge
 *	can set the poll in knowledge from sarshomar
 */
$modules['sarshomar_knowledge'] = array(
	'desc' 			=> T_("Allow to add poll to Sarshomar category"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['add', 'edit', 'view', 'update', 'admin'],
	);


/**
 * show hidden result checkbox
 */
$modules['hidden_result'] = array(
	'desc' 			=> T_("Allow to hide the results"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

/**
 * *********************************************************
 * ****************** NEW PERMISSION LIST ******************
 * *********************************************************
 */

/**
 * free account
 */
$modules['free_account'] =
[
	'desc'        => T_("Use system whitout pay money"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
 * 0 add base money
 */
$modules['free_add_poll'] =
[
	'desc'        => T_("Allow to poll whitout pay money"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
 *
* free_add_brand
*/
$modules['free_add_brand'] =
[
	'desc'        => T_("Allow to brand whitout pay money"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* free_add_filter
*/
$modules['free_add_filter'] =
[
	'desc'        => T_("Allow to add filter whitout pay money"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* free_add_member
*/
$modules['free_add_member'] =
[
	'desc'        => T_("Allow to member whitout pay money"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* lock_edit_mobile
*/
$modules['lock_edit_mobile'] =
[
	'desc'        => T_("Lock edti mobile"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* lock_edit_user_details
*/
$modules['lock_edit_user_details'] =
[
	'desc'        => T_("Lock edit user details"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* lock_edit_username
*/
$modules['lock_edit_username'] =
[
	'desc'        => T_("Lock edit username"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* sarshomar
*/
$modules['sarshomar'] =
[
	'desc'        => T_("Add poll to sarshomar knowledge"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* add_poll_cats
*/
$modules['add_poll_cats'] =
[
	'desc'        => T_("Allow to add poll cats"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* add_poll_article
*/
$modules['add_poll_article'] =
[
	'desc'        => T_("Allow to poll article"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* add_max_tags
*/
$modules['add_max_tags'] =
[
	'desc'        => T_("Allow to add 50 tags"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* max_update_answer
*/
$modules['max_update_answer'] =
[
	'desc'        => T_("Allow to update answer, for example the sarshomar user can update her meny times"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* draft_poll_10
*/
$modules['draft_poll_10'] =
[
	'desc'        => T_("Allow to set 10 draft poll"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* draft_poll_50
*/
$modules['draft_poll_50'] =
[
	'desc'        => T_("Allow to set 50 draft poll"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* draft_poll_500
*/
$modules['draft_poll_500'] =
[
	'desc'        => T_("Allow to set 500 draft poll"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* draft_poll_max
*/
$modules['draft_poll_max'] =
[
	'desc'        => T_("Allow to max poll draft"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* lock_answer_sarshomar_poll
*/
$modules['lock_answer_sarshomar_poll'] =
[
	'desc'        => T_("Lock answer to sarshomar poll"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];

/**
* lock_answer_poll
*/
$modules['lock_answer_poll'] =
[
	'desc'        => T_("Lock answer to poll"),
	'icon'        => 'file-text-o',
	'permissions' => ['view'],
];
return ["modules" => $modules];
?>