<?php
namespace content_api\logintoken;
use \lib\utility;

class model extends \content_api\home\model
{

	public function get_token()
	{
		return $this->token();
	}

	public function post_token()
	{
		return $this->token();
	}

	public function token()
	{
		return ['token' => \lib\utility::hasher('username', null, true)];
	}

	public function get_guest_token()
	{
		return $this->guest_token();
	}

	public function post_guest_token()
	{
		return $this->guest_token();
	}

	public function guest_token(){
		if(utility::header('authorization') || utility::header('Authorization'))
		{
			$api_key = utility::header('authorization') ? utility::header('authorization') : utility::header('Authorization');
			$check = \lib\db\options::get([
				'option_cat' => 'token',
				'option_value' => $api_key,
				'limit' => 1
				]);
			if(empty($check))
			{
				\lib\debug::error('Authorization failed', 'authorization', 'access');
			}
			else
			{
				\lib\db\options::insert([
				'option_cat' => 'token',
				'option_value' => $api_key,
				'limit' => 1
				]);
			}
		}
		else
		{
			\lib\debug::error('Authorization not found', 'authorization', 'access');
		}
	}

}
?>