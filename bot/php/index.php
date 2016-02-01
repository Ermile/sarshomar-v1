<?php
$message = json_decode(file_get_contents('php://input'), true);
file_put_contents('telegram.json', $message, FILE_APPEND);
?>
