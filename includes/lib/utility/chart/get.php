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

		$valid_result_raw   = [];
		$invalid_result_raw = [];
		$cats               = [];
		$return             = [];
		$return['total']    = [];

		if($valid)
		{
			// get the valid result of this poll
			$valid_result_raw   = \lib\db\pollstats::get($_poll_id, ['validation' => 'valid']);
			if(isset($valid_result_raw['total']))
			{
				$return['total']['trust'] = $valid_result_raw['total'];
			}
			else
			{
				$return['total']['trust'] = 0;
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
				$return['total']['untrust'] = (int) $invalid_result_raw['total'];
			}
			else
			{
				$return['total']['untrust'] = 0;
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
				$stats[$key]['value_trust'] = $valid_result_raw['result'][$chart_key];
			}
			else
			{
				$stats[$key]['value'] = 0;
				$stats[$key]['value_trust'] = 0;
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
				$stats[$key]['value_untrust'] = $invalid_result_raw['result'][$chart_key];
			}
			else
			{
				$stats[$key]['value_untrust'] = 0;
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
						$stats[$key][$filter][$male] = $valid_result_raw[$filter][$chart_key][$male];
						$stats[$key][$filter][$male. '_trust'] = $valid_result_raw[$filter][$chart_key][$male];
					}
					else
					{
						$stats[$key][$filter][$male] = 0;
						$stats[$key][$filter][$male. '_trust'] = 0;
					}
				}
			}

			foreach ($stats as $key => $value)
			{
				$chart_key = "opt_". ($key + 1);
				$stats[$key]['cats'] = $cats;
				foreach ($cats as $k => $male)
				{

					if(isset($invalid_result_raw[$filter][$chart_key][$male]))
					{
						if(isset($stats[$key][$filter][$male]))
						{
							$stats[$key][$filter][$male] = (int) $stats[$key][$filter][$male] + (int) $invalid_result_raw[$filter][$chart_key][$male];
						}
						else
						{
							$stats[$key][$filter][$male] = $invalid_result_raw[$filter][$chart_key][$male];
						}
						$stats[$key][$filter][$male. '_untrust'] = $invalid_result_raw[$filter][$chart_key][$male];
					}
					else
					{
						// $stats[$key][$filter][$male] = 0;
						$stats[$key][$filter][$male. '_untrust'] = 0;
					}
				}
			}
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