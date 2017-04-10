<?php
$modules = array();
/**
 * homepage of admin
 */
$modules['admin'] = array(
	'desc' 			=> T_("Allow to show admin page"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['view'],
	);

/**
 * login by everyone users
 */
$modules['everyone_login'] = array(
	'desc' 			=> T_("Allow to login just by mobile"),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['view'],
	);
return ["modules" => $modules];
?>