<?php
$modules = array();
$modules['sarshomar'] = array(
	'desc' 			=> T_('can add, edit poll or survey of sarshomar'),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin'],
	);
return ["modules" => $modules];
?>