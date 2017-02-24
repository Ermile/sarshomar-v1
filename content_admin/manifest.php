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
return ["modules" => $modules];
?>