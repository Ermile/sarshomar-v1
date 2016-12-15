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

return ["modules" => $modules];
?>