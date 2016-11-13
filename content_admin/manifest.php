<?php
$modules = array();

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