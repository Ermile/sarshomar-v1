<?php
namespace content_api\v1\poll\tools;
use \lib\utility;
use \lib\debug;
use \lib\db;

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
	public function poll_get($_options = [])
	{
		if(!debug::$status)
		{
			return ;
		}

		$default_options =
		[
			'check_is_my_poll'   => false,
			'get_filter'         => true,
			'get_opts'           => true,
			'get_options'	     => true,
			'run_options'	     => false,
			'get_public_result'  => true,
			'get_advance_result' => false,
			'type'               => null, // ask || random
		];

		$_options = array_merge($default_options, $_options);

		$result  = [];
		$poll_id = null;
		$poll    = [];
		$need_id = true;

		if($_options['type'] == 'ask')
		{
			$need_id = false;
			$poll    = db\polls::get_last($this->user_id);
		}

		if($_options['type'] == 'random')
		{
			$need_id = false;
			$poll    = db\polls::get_random();
		}

		unset($_options['type']);

		if(utility::request("id") && $need_id)
		{
			$poll_id = utility::request("id");

			if(!$poll_id || !utility\shortURL::is($poll_id))
			{
				return debug::error(T_("Invalid id parameter"), 'id', 'arguments');
			}

			$poll_id = utility\shortURL::decode($poll_id);
			$poll    = db\polls::get_poll($poll_id);
		}
		elseif($need_id && !utility::request("id"))
		{
			return debug::error(T_("Parametr id not set in request"), 'id', 'arguments');
		}

		$result = $this->poll_ready($poll, $_options);

		return $result;
	}
}
?>