<?php
namespace content_api\logintoken;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{

	/**
	 * Gets the token.
	 *
	 * @return     <type>  The token.
	 */
	public function get_token()
	{
		return $this->token();
	}


	/**
	 * Posts a token.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_token()
	{
		return $this->token();
	}


	/**
	 * make token
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function token()
	{
		$username      = utility::request("mobile");
		$authorization = utility::header("authorization") ? utility::header("authorization") : utility::header("Authorization");

		$token = "~Ermile~". $user_id . "_!_". time(). ":)" . rand(1,100);
		$token = utility::hasher($token);
		return ['token' => $token];
	}


	/**
	 * Gets the guest token.
	 *
	 * @return     <type>  The guest token.
	 */
	public function get_guest_token()
	{
		return $this->guest_token();
	}


	/**
	 * Posts a guest token.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_guest_token()
	{
		return $this->guest_token();
	}


	/**
	 * make guest token
	 */
	public function guest_token()
	{

	}



}
?>