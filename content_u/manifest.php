<?php
$modules = array();
/**
 * poll to complete profile
 */
$modules['complete_profile'] = array(
	'desc' 			=> T_("can add the poll to complete the profile"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

/**
 *	sarshomart cat
 *	set the cat of poll on sarshomar
 */
$modules['sarshomar_poll'] = array(
	'desc' 			=> T_("can add the poll by sarshomar cat"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['add', 'edit', 'view', 'update', 'admin'],
	);

/**
 * show hidden result checkbox
 */
$modules['hidden_result'] = array(
	'desc' 			=> T_("can hidden result poll"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

return ["modules" => $modules];
?>