<?php
namespace lib\utility;

class error_logger
{
	public static function log($_data)
	{
		if($_SERVER['SERVER_NAME'] == 'dev.sarshomar.com')
		{
			return false;
		}
		if(!isset($_data["text"]))
		{
			if(!is_string($_data))
			{
				$_data = json_encode($_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			}
			$_data = [
			'chat_id' => "-1001107234508",
			"text" => "```\n" . $_data . "```\n🕰 " . date("Y-m-d H:i:s"),
			"parse_mode" => 'markdown'
			];
			\content\saloos_tg\sarshomar_bot\commands\handle::send_log($_data);
		}
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
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
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