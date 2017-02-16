<?php
namespace content_api\v1\poll\stats\tools;
use \lib\utility;
use \lib\debug;

trait get
{
	/**
	 * get pollstats
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_stats($_options = [])
	{

		$args =
		[
			'get_filter'         => false,
			'get_stats'          => false,
			'get_options'        => false,
			'get_public_result'  => true,
			'get_advance_result' => true,
			'run_options'        => false,
		];

		$poll = $this->poll_get($args);

		$result = [];
		if(isset($poll['stats']))
		{
			$result = $poll['stats'];
		}

		return $result;

	}
}
?>