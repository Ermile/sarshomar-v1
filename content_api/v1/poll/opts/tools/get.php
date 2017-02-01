<?php
namespace content_api\v1\poll\opts\tools;
use \lib\utility;
use \lib\debug;

trait get
{
	/**
	 * get pollopts
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_opts($_options = [])
	{

		$args =
		[
			'get_filter'        => false,
			'get_opts'          => true,
			'get_options'       => false,
			'get_public_result' => false,
			'run_options'       => false,
		];

		$result = $this->poll_get($args);

		if(isset($result['answers']))
		{
			return $result['answers'];
		}
		else
		{
			return null;
		}
	}
}
?>