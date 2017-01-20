<?php
namespace content_u\token;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * the user id
	 *
	 * @var        integer
	 */
	public $user_id = 0;
	public $api_key = null;


	/**
	 * get token data to show
	 */
	public function get_token()
	{
		if(!$this->login("id"))
		{
			return debug::error(T_("Please login to get api token"));
		}

		$this->user_id = $this->login('id');

		return utility\token::get_api_key($this->user_id);
	}


	/**
	 * post data and update or insert token data
	 */
	public function post_token()
	{
		if(!$this->login())
		{
			debug::error(T_("Please login to get api token"));
			return false;
		}

		$this->user_id = $this->login("id");

		debug::msg("api_key", utility\token::create_api_key($this->user_id));

	}

}
?>