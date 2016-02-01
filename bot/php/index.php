<?php
$keyb = array('ReplyKeyboardMarkup' => array('keyboard' => array(array("A", "B"))));
$content = array('chat_id' => "###", 'reply_markup' => $keyb, 'text' => "Test");
echo http_build_query($content);
?>
