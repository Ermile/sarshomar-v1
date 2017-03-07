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
			'filter_chart'       => null
		];

		$charts = \lib\db\pollstats::support_chart();
		$charts = array_keys($charts);

		if(utility::request('type'))
		{
			if(!in_array(utility::request('type'), $charts))
			{
				return debug::error(T_("Invalid chart type"), 'type', 'arguments');
			}

			$args['filter_chart'] = utility::request('type');
		}

		$poll = $this->poll_get($args);

		$result = [];
		if(isset($poll['result']))
		{
			$result = $poll['result'];
		}

		return $result;

	}
}
?>