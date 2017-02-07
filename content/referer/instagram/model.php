<?php
namespace content\referer\instagram;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	function get_instagram()
	{

		$fields = [
		"client_id" 	=> "4ecf7f9c5c4e4e94b4edc80efb10c491",
		"client_secret" => "7d3ea821bf7e4156830ba33d51f8ddc7",
		"grant_type" 	=> "authorization_code",
		"redirect_uri" 	=> "https://dev.sarshomar.com/referer/instagram",
		"code" 			=> \lib\utility::get('code')
		];
		$fields_string = [];
		foreach($fields as $key=>$value)
		{
			$fields_string[] = $key.'='.$value;
		}
		$fields_string = join($fields_string, "&");

		$url = "https://api.instagram.com/oauth/access_token";
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$r = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($r, true);
		if(isset($response['error_type']))
		{
			\lib\debug::error(T_("Token error", 'token', 'instagram'));
			return '';
		}
		$access_token = $response['access_token'];
		$user_id = $_SESSION['user']['id'];
		$instagram_id = $response['user']['id'];
		$q = "INSERT INTO options SET
		user_id = $user_id,
		option_cat = 'instagram',
		option_key = 'id',
		option_value = '$instagram_id'";
		\lib\db::query($q);

		$q = "INSERT INTO options SET
		user_id = $user_id,
		option_cat = 'instagram',
		option_key = 'user_token_$user_id',
		option_value = '$access_token'";
		\lib\db::query($q);

		return $response;
	}
}