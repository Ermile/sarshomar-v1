<?php
namespace lib\utility\chart;
use \lib\debug;

trait get
{

	/**
	 * Gets the result.
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  The result.
	 */
	public static function get_result($_poll_id, $_options = [])
	{

		$default_options =
		[
			'validation' => null,	// default return the valid chart
			// 'filter'     => null, 	// return the master result.
			'filter'     => false, 	// return the master result.
			'port'		 => null,
			'subport'	 => null,
		];

		$_options = array_merge($default_options, $_options);

		$valid   = true; // default the validation is null and return all result [valid and invalid]
		$invalid = true; // default the validation is null and return all result [valid and invalid

		// check the validation the user seted
		if(!is_null($_options['validation']))
		{
			// the user set the valid chart
			// desable invalid chart
			if($_options['validation'] === 'valid')
			{
				$valid   = true;
				$invalid = false;
			}
			// the user set invalid chart
			// disable valid chart
			if($_options['validation'] === 'invalid')
			{
				$valid   = false;
				$invalid = true;
			}
		}

		// get poll ansewrs
		$answers = \lib\db\pollopts::get($_poll_id);
		$poll    = \lib\db\polls::get_poll($_poll_id);

		$result = [];

		// get the poll chart title
		$title = T_("Untitled poll");
		if(isset($poll['title']))
		{
			$title = $poll['title'];
		}
		// set the title to return
		$result['title'] = $title;

		// get the poll chart url
		$url = "#";

		if(isset($poll['url']))
		{
			$url = $poll['url'];
		}

		$result['url'] = $url;

		$filters = array_keys(\lib\db\pollstats::support_chart());

		$filter = null;
		if(is_null($_options['filter']) || !$_options['filter'])
		{
			$filter = 'result';
		}
		elseif(is_string($_options['filter']) && in_array($_options['filter'], $filters))
		{
			$filter = $_options['filter'];
		}
		else
		{
			return debug::error(T_("Invalid chart filter"), 'db', 'system');
		}

		$valid_result_raw           = [];
		$invalid_result_raw         = [];
		$cats                       = [];
		$return                     = [];
		$return['summary']          = [];
		$return['summary']['total'] = 0;

		if($valid)
		{
			// get the valid result of this poll
			$valid_result_raw   = \lib\db\pollstats::get($_poll_id, ['validation' => 'valid']);
			if(isset($valid_result_raw['total']))
			{
				$return['summary']['reliable'] = $valid_result_raw['total'];
			}
			else
			{
				$return['summary']['reliable'] = 0;
			}

			$temp_valid_result_raw = [];
			if(isset($valid_result_raw['result']))
			{
				$temp_valid_result_raw['result'] = $valid_result_raw['result'];
			}

			if(isset($valid_result_raw[$filter]))
			{
				$cats = self::find_cats($valid_result_raw[$filter], $cats);
				$temp_valid_result_raw[$filter] = $valid_result_raw[$filter];
			}

			$valid_result_raw = $temp_valid_result_raw;

		}

		if($invalid)
		{
			// get the invalid result
			$invalid_result_raw = \lib\db\pollstats::get($_poll_id, ['validation' => 'invalid']);
			if(isset($invalid_result_raw['total']))
			{
				$return['summary']['unreliable'] = (int) $invalid_result_raw['total'];
			}
			else
			{
				$return['summary']['unreliable'] = 0;
			}
			$temp_invalid_result_raw = [];
			if(isset($invalid_result_raw['result']))
			{
				$temp_invalid_result_raw['result'] = $invalid_result_raw['result'];
			}

			if(isset($invalid_result_raw[$filter]))
			{
				$cats = self::find_cats($invalid_result_raw[$filter], $cats);
				$temp_invalid_result_raw[$filter] = $invalid_result_raw[$filter];
			}

			$invalid_result_raw = $temp_invalid_result_raw;
		}

		// reliable
		// unreliable
		// $cats = array_unique($cats);

		$stats = [];
		foreach ($answers as $key => $value)
		{
			if(isset($value['key']))
			{
				$stats[$key]['key'] = $value['key'];
			}

			if(isset($value['title']))
			{
				$stats[$key]['title'] = $value['title'];
			}

			if(isset($value['attachment']))
			{
				$stats[$key]['file'] = \lib\utility\shortURL::encode($value['attachment']);
			}
		}

		foreach ($stats as $key => $value)
		{
			$chart_key = "opt_". ($key + 1);
			if(isset($valid_result_raw['result'][$chart_key]))
			{
				$stats[$key]['value'] = $valid_result_raw['result'][$chart_key];
				$stats[$key]['reliable'] = $valid_result_raw['result'][$chart_key];
			}
			else
			{
				$stats[$key]['value'] = 0;
				$stats[$key]['reliable'] = 0;
			}
		}

		foreach ($stats as $key => $value)
		{
			$chart_key = "opt_". ($key + 1);
			if(isset($invalid_result_raw['result'][$chart_key]))
			{
				if(isset($stats[$key]['value']))
				{
					$stats[$key]['value'] = (int) $stats[$key]['value'] + (int) $invalid_result_raw['result'][$chart_key];
				}
				else
				{
					$stats[$key]['value'] = $invalid_result_raw['result'][$chart_key];
				}
				$stats[$key]['unreliable'] = $invalid_result_raw['result'][$chart_key];
			}
			else
			{
				$stats[$key]['unreliable'] = 0;
			}
		}

		/**
		 * filter chart
		 */
		if($_options['filter'])
		{
			foreach ($stats as $key => $value)
			{
				$chart_key = "opt_". ($key + 1);
				$stats[$key]['cats'] = $cats;
				foreach ($cats as $k => $male)
				{
					if(isset($valid_result_raw[$filter][$chart_key][$male]))
					{
						$stats[$key][$filter][$male]['title']       = $male;
						$stats[$key][$filter][$male]['value']       = $valid_result_raw[$filter][$chart_key][$male];
						$stats[$key][$filter][$male]['reliable']    = $valid_result_raw[$filter][$chart_key][$male];
						$return['summary'][$filter][$male]['title'] = $male;

						if(isset($return['summary'][$filter][$male]['total']))
						{
							$return['summary'][$filter][$male]['total'] =
							(int) $return['summary'][$filter][$male]['total'] + (int) $valid_result_raw[$filter][$chart_key][$male];
						}
						else
						{
							$return['summary'][$filter][$male]['total'] = $valid_result_raw[$filter][$chart_key][$male];
						}

						if(isset($return['summary'][$filter][$male]['reliable']))
						{
							$return['summary'][$filter][$male]['reliable'] =
							(int) $return['summary'][$filter][$male]['reliable'] + (int) $valid_result_raw[$filter][$chart_key][$male];
						}
						else
						{
							$return['summary'][$filter][$male]['reliable'] = $valid_result_raw[$filter][$chart_key][$male];
						}
					}
					else
					{
						$stats[$key][$filter][$male]['title']       = $male;
						$stats[$key][$filter][$male]['value']       = 0;
						$stats[$key][$filter][$male]['reliable']    = 0;
						$return['summary'][$filter][$male]['title'] = $male;

						if(isset($return['summary'][$filter][$male]['total']))
						{
							$return['summary'][$filter][$male]['total'] = (int) $return['summary'][$filter][$male]['total'];
						}
						else
						{
							$return['summary'][$filter][$male]['total'] = 0;
						}

						if(isset($return['summary'][$filter][$male]['reliable']))
						{
							$return['summary'][$filter][$male]['reliable'] = (int) $return['summary'][$filter][$male]['reliable'];
						}
						else
						{
							$return['summary'][$filter][$male]['reliable'] = 0;
						}
					}
				}
			}

			foreach ($stats as $key => $value)
			{
				$chart_key = "opt_". ($key + 1);
				$stats[$key]['cats'] = $cats;
				foreach ($cats as $k => $male)
				{

					$stats[$key][$filter][$male]['title'] = $male;
					if(isset($invalid_result_raw[$filter][$chart_key][$male]))
					{
						if(isset($stats[$key][$filter][$male]['value']))
						{
							$stats[$key][$filter][$male]['value'] = (int) $stats[$key][$filter][$male]['value'] + (int) $invalid_result_raw[$filter][$chart_key][$male];
							$return['summary'][$filter][$male]['title'] = $male;

							if(isset($return['summary'][$filter][$male]['total']))
							{
								$return['summary'][$filter][$male]['total'] =
								(int) $return['summary'][$filter][$male]['total'] + (int) $invalid_result_raw[$filter][$chart_key][$male];
							}
							else
							{
								$return['summary'][$filter][$male]['total'] = $invalid_result_raw[$filter][$chart_key][$male];
							}

							if(isset($return['summary'][$filter][$male]['unreliable']))
							{
								$return['summary'][$filter][$male]['unreliable'] =
								(int) $return['summary'][$filter][$male]['unreliable'] + (int) $invalid_result_raw[$filter][$chart_key][$male];
							}
							else
							{
								$return['summary'][$filter][$male]['unreliable'] = $invalid_result_raw[$filter][$chart_key][$male];
							}
						}
						else
						{
							$stats[$key][$filter][$male]['value'] = $invalid_result_raw[$filter][$chart_key][$male];

							if(isset($return['summary'][$filter][$male]['total']))
							{
								$return['summary'][$filter][$male]['total'] =
								(int) $return['summary'][$filter][$male]['total'] + (int) $invalid_result_raw[$filter][$chart_key][$male];
							}
							else
							{
								$return['summary'][$filter][$male]['total'] = $invalid_result_raw[$filter][$chart_key][$male];
							}

							if(isset($return['summary'][$filter][$male]['unreliable']))
							{
								$return['summary'][$filter][$male]['unreliable'] =
								(int) $return['summary'][$filter][$male]['unreliable'] + (int) $invalid_result_raw[$filter][$chart_key][$male];
							}
							else
							{
								$return['summary'][$filter][$male]['unreliable'] = $invalid_result_raw[$filter][$chart_key][$male];
							}
						}
						$stats[$key][$filter][$male]['unreliable'] = $invalid_result_raw[$filter][$chart_key][$male];
					}
					else
					{
						$stats[$key][$filter][$male]['unreliable'] = 0;
					}
				}
			}
		}
		foreach ($stats as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $k => $v)
				{
					if(is_array($v))
					{
						sort($stats[$key][$k]);
					}
				}
			}
		}

		if(isset($return['summary']))
		{
			$return['summary']['total'] = array_sum($return['summary']);
		}

		if(isset($return['summary'][$filter]))
		{
			sort($return['summary'][$filter]);
		}

		$return['answers'] = $stats;
		return $return;
	}



	/**
	 * find cats from result
	 *
	 * @param      <type>  $_data         The data
	 * @param      <type>  $_finded_cats  The finded cats
	 */
	public static function find_cats($_data, $_finded_cats)
	{
		foreach ($_data as $key => $value)
		{
			if(is_array($value))
			{
				foreach ($value as $k => $v)
				{
					if(!in_array($k, $_finded_cats))
					{
						array_push($_finded_cats, $k);
					}
				}
			}
		}
		return $_finded_cats;
	}

}
?>