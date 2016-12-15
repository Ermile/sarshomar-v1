<?php
$modules = array();

/**
 * accept and publish poll
 */
$modules['publish_poll'] = array(
	'desc' 			=> T_("Allow to change the post status to publi"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['admin'],
	);

return ["modules" => $modules];
?>