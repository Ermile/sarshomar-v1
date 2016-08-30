<?php
$modules = array();
$modules['test'] = array(
	'desc' 			=> T_('Use posts to share your news in specefic category'),
	'icon'			=> 'file-text-o',
	'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin'],
	);
return ["modules" => $modules];
?>