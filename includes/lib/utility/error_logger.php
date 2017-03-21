<?php
namespace lib\utility;

class error_logger
{
	function log($_data)
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
}
?>