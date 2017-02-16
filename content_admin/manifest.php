<?php
$modules = array();
/**
 * homepage of admin
 */
$modules['admin'] = array(
	'desc' 			=> T_("Allow to show admin page"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

/**
 * accept and publish poll
 */
$modules['publish_poll'] = array(
	'desc' 			=> T_("Allow to change the post status to publish"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

return ["modules" => $modules];
?>