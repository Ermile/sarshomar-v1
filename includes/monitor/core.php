<?php
require_once '../lib/utility/error_logger.php';
function core($_addr)
{
	$body = file_get_contents($_addr);
	$body = trim($body);
	$body = preg_replace("\n{2,}", "\n", $body);
	$peroperty = [];
	foreach (explode("\n", $body) as $key => $value) {
		$split = explode(":", $value);
		$peroperty[strtolower(trim($split[0]))] = isset($split[1]) ? trim($split[1]) : null;
	}
	return $peroperty;
}
?>