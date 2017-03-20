<?php
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

function telegram($_data)
{
	$ch = curl_init();
	if ($ch === false)
	{
		return 'Curl failed to initialize';
	}
	$curlConfig = array(
		CURLOPT_URL            => "https://api.telegram.org/bot215239661:AAHyVstYPXKJyfhDK94A-XfYukDMiy3PLKY/sendMessage",
		CURLOPT_POST           => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SAFE_UPLOAD    => true,
		CURLOPT_SSL_VERIFYPEER => false
		);
	curl_setopt_array($ch, $curlConfig);
	$data_string = json_encode($_data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string))
	);

	$result = curl_exec($ch);
	$result_decode = json_decode($result, true);
	curl_close($ch);
	return $result_decode;
}
?>