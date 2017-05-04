<?php
$modules = array();

$modules['admin'] =
[
	'desc'        => T_("Allow to add the election, candida and edit it"),
	'icon'        => 'file-text-o',
	'permissions' => ['admin'],
];


$modules['data'] =
[
	'desc'        => T_("Allow to add the election data and edit it"),
	'icon'        => 'file-text-o',
	'permissions' => ['admin'],
];

return ["modules" => $modules];
?>