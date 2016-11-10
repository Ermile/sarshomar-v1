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
 *	sarshomart knowledge
 *	can set the poll in knowledge from sarshomar
 */
$modules['sarshomar_knowledge'] = array(
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


/**
 * accept and publish poll
 */
$modules['publish_poll'] = array(
	'desc' 			=> T_("can change polls status to publish"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

return ["modules" => $modules];
?>