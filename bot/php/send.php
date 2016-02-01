<?php
$url = "https://api.telegram.org/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/sendMessage";

$keyb = array('ReplyKeyboardMarkup' => array('keyboard' => array(array("A", "B"))));
$content = array('chat_id' => 58164083, 'reply_markup' => $keyb, 'text' => "Test");

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
curl_close ($ch);
file_put_contents("telegram.json", $server_output);
?>
<!-- 
ttps://api.telegram.org/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/sendMessage?
chat_id=46898544
reply_markup={"keyboard%22:[[%22a%22],[%22b%22]]}
text=Test -->