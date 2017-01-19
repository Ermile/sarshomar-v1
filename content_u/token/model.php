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

		$where =
		[
			'user_id'       => $this->user_id,
			'option_cat'    => 'token',
			'option_key'    => 'api_key',
			'option_status' => 'enable',
			'limit'         => 1
		];
		$api_key = \lib\db\options::get($where);

		if($api_key && isset($api_key[0]['value']))
		{
			return $api_key[0]['value'];
		}
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

		$this->destroy_token();

		$this->create_token();

		return debug::msg("api_key", $this->api_key);
	}


	/**
	 * destroy token
	 */
	private function destroy_token()
	{
		$where =
		[
			'user_id'    => $this->user_id,
			'option_cat' => 'token',
			'option_key' => 'api_key'
		];
		$set = ['option_status' => 'disable'];
		\lib\db\options::update_on_error($set, $where);
	}


	/**
	 * Creates a token.
	 */
	private function create_token()
	{
		$api_key = "!~~!". $this->user_id. ':_$_:'. time(). "*^*". rand(2, 200);
		$api_key = utility::hasher($api_key);
		$api_key = md5($api_key);
		$arg =
		[
			'user_id'      => $this->user_id,
			'option_cat'   => 'token',
			'option_key'   => 'api_key',
			'option_value' => $api_key
		];
		$set = \lib\db\options::insert($arg);
		if($set)
		{
			$this->api_key = $api_key;
		}
	}
}
?>