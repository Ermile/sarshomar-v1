<?php
$exec = exec('curl -X GET -H "Content-Type: application/json" -H "Cache-Control: no-cache" -H "Postman-Token: ae91638b-0ec4-b416-6a61-5030980be481" "https://api.telegram.org/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/getWebhookinfo"');
$x = json_decode($exec, true);
echo "<font color='#4e9a06'>" . $x['result']['pending_update_count'] . "</font>";
?>