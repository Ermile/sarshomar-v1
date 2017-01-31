<?php
header("Content-Type: text/plain");
echo file_get_contents(root . 'includes/cls/database/log/log.sql');
exit();
?>