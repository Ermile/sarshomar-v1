<?php
namespace lib\utility\chart;

trait get
{
	/**
	 * Gets the result.
	 * by hicharts syntax mod
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  The result.
	 */
	public static function get_result($_poll_id, $_options = [])
	{
		$result = [];
		$default_options =
		[
			'highcharts' => true, 	// default return in highcharts mod
			'drilldown'  => 3,    	// set the chart to drilldown mod if count of categories in one opt > 3
			'validation' => null,	// default return the valid chart
			'filter'     => null, 	// retrun the master result.
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

		// get poll meta to get all opt of this poll
		$poll = \lib\db\polls::get_poll($_poll_id);

		// we can not found meta of this poll
		if(!isset($poll['meta']) || empty($poll))
		{
			// we must return false to this check
			// but when return false the JavaScript language have a bug!
			// so we return the empty array and set json to fix the JavaScript bug
			return json_encode([], JSON_UNESCAPED_UNICODE);
		}
		// get the poll chart title
		$title = "Untitled Poll";
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

		// get the categories
		$categories = [];
		// list of main opt
		// maybe the user not answered to some of opt
		// we need to get all opt to load basic chart
		$opt_list   = [];
		if(isset($poll['meta']['opt']) && is_array($poll['meta']['opt']))
		{
			$categories = array_column($poll['meta']['opt'], 'txt');
			$opt_list   = array_column($poll['meta']['opt'], 'key', 'txt');
		}
		// set the categories to return
		$result['categories'] = json_encode($categories,JSON_UNESCAPED_UNICODE);

		// the opt list of this poll
		$opt_list = array_flip($opt_list);

		// basci chart series
		$basic_series       = [];
		// valid result
		$valid_result_raw   = [];
		$valid_basic_series = [];

		if($valid)
		{
			// get the valid result of this poll
			$valid_result_raw   = \lib\db\pollstats::get($_poll_id, ['validation' => 'valid']);

			if(isset($valid_result_raw['result']) && is_array($valid_result_raw['result']))
			{
				$valid_basic_series = array_merge($opt_list, $valid_result_raw['result']);
			}
			// this syntax from highcharts
			$basic_series[] = ['name' => 'valid', 'data' => array_values($valid_basic_series)];
		}
		// invalid result
		$invalid_result_raw = [];
		$invalid_basic_series = [];

		if($invalid)
		{
			// get the invalid result
			$invalid_result_raw = \lib\db\pollstats::get($_poll_id, ['validation' => 'invalid']);
			if(isset($invalid_result_raw['result']) && is_array($invalid_result_raw['result']))
			{
				$invalid_basic_series = array_merge($opt_list, $invalid_result_raw['result']);
			}
			// this syntax form highcharts
			$basic_series[] = ['name' => 'invalid', 'data' => array_values($invalid_basic_series)];
		}

		// check the highcharts mode is true or false
		// if the highcharts mod is false we return the raw result of polls
		if($_options['highcharts'] === false)
		{
			$tmp_result = [];
			if($valid)
			{
				$tmp_result['valid'] = $valid_result_raw;
			}
			if($invalid)
			{
				$tmp_result['invalid'] = $invalid_result_raw;
			}
			// return the raw result of polls
			return $tmp_result;
		}

		// basic chart is ready to use
		$result['basic']      = json_encode($basic_series, JSON_UNESCAPED_UNICODE);

		// get the support filter to load chart of this filter
		$support_filter = \lib\utility\filters::support_filter();
		$support_filter = array_keys($support_filter);

		// check the filter of users was request form system
		// maybe one filter seted to load chart
		// on this mod set the $_option['filter'] = (string) [the filter]
		// also maybe the users seted some filter
		// on this mode set array to get this fitlter
		// for examplte $_options['filter'] = ['gender', 'age', 'city']
		if(!is_null($_options['filter']))
		{
			// load some filter
			if(is_array($_options['filter']))
			{
				$tmp_support_filter = [];
				foreach ($_options['filter'] as $key => $value)
				{
					if(in_array($value, $support_filter))
					{
						$tmp_support_filter[] = $value;
					}
				}
				$support_filter = $tmp_support_filter;
			}
			// load one filter
			elseif(is_string($_options['filter']))
			{
				if(in_array($_options['filter'], $support_filter))
				{
					$support_filter = [$_options['filter']];
				}
			}
		}

		// ready the variable
		$main_drilldown_series = [];
		$drilldown_series      = [];
		$stacked_series        = [];
		$count_check           = [];

		// for every filter run this
		// this syntax in highcharts mode
		// got to http://www.highcharts.com/ to find what is it. :|
		foreach ($support_filter as $key => $filter)
		{
			$count_check[$filter] = 0;
			$merge = [];
			$i     = 0;
			if($valid)
			{
				if(isset($valid_result_raw[$filter]) && is_array($valid_result_raw[$filter]))
				{
					foreach ($valid_result_raw[$filter] as $title => $value)
					{
						$merge = array_merge($merge, $value);
						if(count($value) > $count_check[$filter])
						{
							$count_check[$filter] = count($value);
						}
					}
					// drilldown series
					$main_drilldown_series[$filter]['valid'] = [];
					$main_drilldown_series[$filter]['valid']['data'] = [];

					// stacked series
					$stacked_series[$filter] = [];
					foreach ($merge as $title => $index)
					{
						$stacked_series[$filter][$i]['name']  = $title;
						$stacked_series[$filter][$i]['data']  = [];
						$stacked_series[$filter][$i]['stack'] = 'valid';

						foreach ($opt_list as $opt_key => $opt_text)
						{
							$main_drilldown_series[$filter]['valid']['data'][$opt_key]['name'] = $opt_text;
							$main_drilldown_series[$filter]['valid']['data'][$opt_key]['y'] =
								isset($valid_result_raw['result'][$opt_key]) ?
									$valid_result_raw['result'][$opt_key] : 0;
							$main_drilldown_series[$filter]['valid']['data'][$opt_key]['drilldown'] = "valid_". $opt_key;

							if(isset($valid_result_raw[$filter][$opt_key][$title]))
							{
								array_push($stacked_series[$filter][$i]['data'],
									$valid_result_raw[$filter][$opt_key][$title]);
							}
							else
							{
								array_push($stacked_series[$filter][$i]['data'], 0);

							}
						}
						$i++;
					}
				}
			}

			if($invalid)
			{
				// drilldown series
				$main_drilldown_series[$filter]['invalid'] = [];
				$main_drilldown_series[$filter]['invalid']['data'] = [];
				if(isset($invalid_result_raw[$filter]) && is_array($invalid_result_raw[$filter]))
				{
					foreach ($invalid_result_raw[$filter] as $title => $value)
					{
						$merge = array_merge($merge, $value);
						if(count($value) > $count_check[$filter])
						{
							$count_check[$filter] = count($value);
						}
					}
					// $stacked_series[$filter] = [];

					foreach ($merge as $title => $index)
					{
						$stacked_series[$filter][$i]['name']  = $title;
						$stacked_series[$filter][$i]['data']  = [];
						$stacked_series[$filter][$i]['stack'] = 'invalid';
						foreach ($opt_list as $opt_key => $opt_text)
						{

							$main_drilldown_series[$filter]['invalid']['data'][$opt_key]['name'] = $opt_text;
							$main_drilldown_series[$filter]['invalid']['data'][$opt_key]['y'] =
								isset($valid_result_raw['result'][$opt_key]) ?
									$invalid_result_raw['result'][$opt_key] : 0;
							$main_drilldown_series[$filter]['invalid']['data'][$opt_key]['drilldown'] = "invalid_". $opt_key;

							if(isset($invalid_result_raw[$filter][$opt_key][$title]))
							{
								array_push($stacked_series[$filter][$i]['data'],
									$invalid_result_raw[$filter][$opt_key][$title]);
							}
							else
							{
								array_push($stacked_series[$filter][$i]['data'], 0);
							}
						}
						$i++;
					}
				}
			}
			$tmp = [];
			if(isset($stacked_series[$filter]) && is_array($stacked_series[$filter]))
			{
				foreach ($stacked_series[$filter] as $key => $value)
				{
					$tmp[] = $value;
				}
			}
			$stacked_series[$filter] = [];
			$stacked_series[$filter] = json_encode($tmp, JSON_UNESCAPED_UNICODE);


			$main_drilldown_tmp = [];
			if(isset($main_drilldown_series[$filter]) && is_array($main_drilldown_series[$filter]))
			{
				foreach ($main_drilldown_series[$filter] as $key => $value)
				{
					if(isset($value['data']) && is_array($value['data']))
					{
						$tmp = [];
						foreach ($value['data'] as $k => $v)
						{
							$tmp[] = $v;
						}
						$main_drilldown_tmp[] = ['name' => $key, 'data' => $tmp];
					}
				}
			}
			$main_drilldown_series[$filter] = json_encode($main_drilldown_tmp, JSON_UNESCAPED_UNICODE);

			$j = 0;
			$drilldown_series[$filter][$j] = [];
			if($valid)
			{
				if(isset($valid_result_raw[$filter]) && is_array($valid_result_raw[$filter]))
				{
					foreach ($valid_result_raw[$filter] as $opt_key => $value)
					{
						if(is_array($value))
						{
							$tmp = [];
							foreach ($value as $title => $count)
							{
								array_push($tmp, [$title, $count]);
							}
							if(isset($opt_list[$opt_key]))
							{
								$drilldown_series[$filter][$j] =
									[
										'name' => $opt_list[$opt_key],
										'id' => 'valid_'. $opt_key,
										'data' => $tmp
									];
								$j++;
							}
						}
					}
				}
			}
			if($invalid)
			{
				if(isset($invalid_result_raw[$filter]) && is_array($invalid_result_raw[$filter]))
				{
					foreach ($invalid_result_raw[$filter] as $opt_key => $value)
					{
						if(is_array($value))
						{
							$tmp = [];
							foreach ($value as $title => $count)
							{
								array_push($tmp, [$title, $count]);
							}

							if(isset($opt_list[$opt_key]))
							{
								$drilldown_series[$filter][$j] =
								[
									'name' => $opt_list[$opt_key],
									'id' => 'invalid_'. $opt_key,
									'data' => $tmp
								];
								$j++;
							}
						}
					}
				}
			}
			if(isset($drilldown_series[$filter]))
			{
				$drilldown_series[$filter] = json_encode($drilldown_series[$filter], JSON_UNESCAPED_UNICODE);
			}
		}
		$result['stacked']          = $stacked_series;
		$result['drilldown_series'] = $main_drilldown_series;
		$result['drilldown']        = $drilldown_series;
		return $result;
	}
}
?>