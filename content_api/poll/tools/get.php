<?php
namespace content_api\poll\tools;
use \lib\utility;

trait get
{
	/**
	 * get a post
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function get($_options = [])
	{
		$default_options = 
		[
			'get_filter'         => true,
			'get_opts'           => true,
			'get_public_result'  => true,
			'get_advance_result' => false,
		];

		$_options = array_merge($default_options, $_options);

		$result  = [];
		$poll_id = null;
		$poll = [];
		if(utility::request("type"))
		{
			switch (utility::request("type")) 
			{
				case 'ask':
					if(!$this->login("id"))
					{
						return \lib\debug::error(T_("Please login and run ask"), 'type', 'login');
					}

					$poll = \lib\db\polls::get_last($this->login("id"));
					break;

				case 'random':
					$poll = \lib\db\polls::get_random();
					break;

				default:
					return \lib\debug::error(T_("Invalid parametr type"), 'type', 'arguments');
					break;
			}
		}
		elseif(utility::request("id"))
		{
			$poll_id = utility::request("id");

			if(!$poll_id)
			{
				return \lib\debug::error(T_("poll id not found"), 'id', 'arguments');
			}

			if(!preg_match("/^[". utility\shortURL::ALPHABET ."]+$/", $poll_id))
			{
				return \lib\debug::error(T_("Invalid parametr id"), 'id', 'arguments');
			}
			
			$poll_id = \lib\utility\shortURL::decode($poll_id);

			$poll    = \lib\db\polls::get_poll($poll_id);
		}
		
		$result = $this->ready_poll($poll, $_options);

		return $result;
	}
}
?>