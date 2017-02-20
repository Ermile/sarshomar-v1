<?php
namespace lib\utility\chart;

trait set
{
	/**
	 * set answered count to post meta
	 *
	 * @param      <type>  $_poll_id
	 */
	public static function set_poll_result($_args)
	{

		$default_args =
		[
			'poll_id'      => null,
			'user_id'      => null,
			'validation'   => null,
			'opt_key'      => null,
			'opt_txt'      => null,
			'type'         => null,
			'update_mode'  => null,
			'port'         => null,
			'subport'      => null,
			'profile'      => null,
			'first_answer' => true,
		];

		$_args = array_merge($default_args, $_args);


		// get the poll id
		if(!$_args['poll_id'])
		{
			return debug::error(T_("Poll id not set"), 'set_poll_result', 'db');
		}
		$poll_id = $_args['poll_id'];


		// get the user id
		if(!$_args['user_id'])
		{
			return debug::error(T_("User id not set"), 'set_poll_result', 'db');
		}
		$user_id = $_args['user_id'];


		// get the validation result
		$validation = 'invalid';
		if($_args['validation'])
		{
			$validation = $_args['validation'];
		}

		// default of chart is not sorting poll
		$sorting  = false;
		// key = the opt_key and value = the sort index
		$sort_opt = [];
		// check the opt keys
		if(isset($_args['opt_key']))
		{
			// is array opt key mean we be in sorting mode
			if(is_array($_args['opt_key']))
			{
				// example of $_args[opt_key] : [1,2,3,4,5] || [5,4,3,2,1] the sorting mode
				$sorting = true;
				foreach ($_args['opt_key'] as $key => $value)
				{
					$sort_opt['opt_'. $value] = count($_args['opt_key']) - $key;
				}
				// example of $sort_opt =
				// [
				// 	opt_1 => 5,
				// 	opt_2 => 4,
				// 	opt_3 => 3,
				// 	opt_4 => 2,
				// 	opt_5 => 1
				// ];
			}
			// default poll and not sorting mode
			else
			{
				$opt_key = 'opt_'. $_args['opt_key'];
			}
		}
		else
		{
			return false;
		}

		// check the opt_text
		$opt_txt = null;
		if(isset($_args['opt_txt']))
		{
			$opt_txt = $_args['opt_txt'];
		}

		// default mode is plus the chart
		$plus = true;
		// check the type of change chart : plus | minus the chart
		// in sorting mode we have not minus type of change chart
		if(isset($_args['type']) && $_args['type'] != 'plus')
		{
			$plus = false;
		}

		$update_mode = false;
		// default answer of users not in update mode
		// but when the user update her answer
		// we must update the user answer
		// and we don't plus the total of pollstats
		if(isset($_args['update_mode']) && $_args['update_mode'] === true)
		{
			$update_mode = true;
		}

		// default port of user answer is 'site'
		$port = "'site'";
		if(isset($_args['port']))
		{
			$port = "'". $_args['port']. "'";
		}

		// default subport of the user answer is NULL, this method use in telegram mode
		$subport = "NULL";
		if(isset($_args['subport']))
		{
			$subport = "'". $_args['subport']. "'";
		}



		// user skip the poll
		// neelless to change the chart
		// and this check must be after set sarshomar_total_answered
		// becaus the user see the poll and answer to this
		// but the answer of this user needless to change the chart
		if($opt_key == "opt_0")
		{
			return true;
		}

		// the user profile data to make chart by this items
		$user_profile_data = [];

		// in minus mode we set the profile
		// and we shuld not get the current user profile
		// we get the profile of users has been answered by this profile
		// and load old profile data to minus the chart
		if(isset($_args['profile']))
		{
			// get profile data in filter table
			$user_profile_data = \lib\db\filters::get($_args['profile']);
			if(is_array($user_profile_data))
			{
				// remove empty value from profile to minus the 'undefined' of chart
				$user_profile_data = array_filter($user_profile_data);
			}
		}
		// the profile not set
		// we get the current profile data of users
		else
		{
			// get the current profile data of users
			$user_profile_data = \lib\utility\profiles::get_profile_data($user_id);
		}
		// get the support chart of service
		// some index of profile data we have not eny chart of this
		// we have the chart of all index in pollstats::support_chart()
	    $support_chart = \lib\db\pollstats::support_chart();

	    // the keys of support_charts is important
	    // the value of this array use in other place
	    $support_chart = array_keys($support_chart);

	    // get the poll stats record to open the chart and change it
		$pollstats = \lib\db\pollstats::get($poll_id, ['validation' => $validation]);
		// if the poll stats record is find
		// we must be change the chart
		// and when the poll stats not found we must creat the chart
		if($pollstats && is_array($pollstats))
		{
			// set the update mode to run update query
			$update_pollstat_record = true;
			$pollstats              = $pollstats;
		}
		else
		{
			// set the insert mod to run insert query
			$update_pollstat_record = false;
			$pollstats              = [];
		}

		// update mode
		// we update the chart
		$set = [];
		// plus the total answered of this poll
		if(isset($pollstats['total']) && $pollstats['total'])
		{
			// in plus mode we ++ the total answered to this poll
			// in minus mode we not change the total field
			if($plus && !$update_mode)
			{
				$pollstats['total']++;
			}
		}
		// first times to set the total fields
		else
		{
			$pollstats['total'] = 1;
		}
		// set the pollstats.total field in query
		$set[] = " pollstats.total = ". $pollstats['total'];

		// if we in sorting mode:
		// update all opt of this poll
		// all opt of this poll was plused by sort index value
		if($sorting)
		{
			// update all index of opt of this poll
			foreach ($sort_opt as $opt => $sort_index)
			{
				if(isset($pollstats['result'][$opt]))
				{
					$pollstats['result'][$opt] += $sort_index;
				}
				else
				{
					$pollstats['result'][$opt] = $sort_index;
				}
			}
		}
			 // we not in sorting mode
		else //
		     // we plus one opt of this poll
		{
			if(isset($pollstats['result'][$opt_key]))
			{
				if($plus)
				{
					$pollstats['result'][$opt_key]++;
				}
				else
				{
					if(intval($pollstats['result'][$opt_key]) > 0)
					{
						$pollstats['result'][$opt_key]--;
					}
				}
			}
			else
			{
				if($plus)
				{
					$pollstats['result'][$opt_key] = 1;
				}
			}
		}

		// update the result field in table
		$set[] = " pollstats.result = '". json_encode($pollstats['result'], JSON_UNESCAPED_UNICODE). "'";

		// for each support chart do this:
		foreach ($support_chart as $key => $filter)
		{
			// check the user have this filter or no
			// if the users have this filter:
			if(isset($user_profile_data[$filter]) && \lib\db\pollstats::support_chart($filter, $user_profile_data[$filter]))
			{
				// if in sorting mode we update all opt index of this poll
				if($sorting)
				{
					// update all opt index of this poll
					foreach ($sort_opt as $opt => $sort_index)
					{
						if(isset($pollstats[$filter][$opt]))
						{
							if(isset($pollstats[$filter][$opt][$user_profile_data[$filter]]))
							{
								$pollstats[$filter][$opt][$user_profile_data[$filter]]+= $sort_index;
							}
							else
							{
								$pollstats[$filter][$opt][$user_profile_data[$filter]] = $sort_index;
							}
						}
						else
						{
							$pollstats[$filter][$opt][$user_profile_data[$filter]] = $sort_index;
						}
					}
				}
					 // we not in sorting mode
				else // update one opt of this poll
				     //
				{
					// check the filter of this opt
					if(isset($pollstats[$filter][$opt_key]))
					{
						if(isset($pollstats[$filter][$opt_key][$user_profile_data[$filter]]))
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key][$user_profile_data[$filter]]++;
							}
							else
							{
								if(intval($pollstats[$filter][$opt_key][$user_profile_data[$filter]]) > 1)
								{
									$pollstats[$filter][$opt_key][$user_profile_data[$filter]]--;
								}
								else
								{
									unset($pollstats[$filter][$opt_key][$user_profile_data[$filter]]);
								}
							}
						}
						else
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key][$user_profile_data[$filter]] = 1;
							}
						}
					}
					else
					{
						if($plus)
						{
							$pollstats[$filter][$opt_key][$user_profile_data[$filter]] = 1;
						}
					}
				}
			}
				 // the user not set this filter
			else //
			     // we set this item of chart as 'undefined'
			{
				// if in sorting mode we update all opt index of this poll
				if($sorting)
				{
					foreach ($sort_opt as $opt => $sort_index)
					{
						if(isset($pollstats[$filter][$opt]))
						{
							if(isset($pollstats[$filter][$opt]['undefined']))
							{

								$pollstats[$filter][$opt]['undefined']+= $sort_index;
							}
							else
							{
								$pollstats[$filter][$opt]['undefined'] = $sort_index;
							}
						}
						else
						{
							$pollstats[$filter][$opt]['undefined'] = $sort_index;
						}
					}
				}
				else // we not in sorting mode
				{
					if(isset($pollstats[$filter][$opt_key]))
					{
						if(isset($pollstats[$filter][$opt_key]['undefined']))
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key]['undefined']++;
							}
							else
							{
								if(intval($pollstats[$filter][$opt_key]['undefined']) > 1)
								{
									$pollstats[$filter][$opt_key]['undefined']--;
								}
								else
								{
									unset($pollstats[$filter][$opt_key]['undefined']);
								}
							}
						}
						else
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key]['undefined'] = 1;
							}
						}
					}
					else
					{
						if($plus)
						{
							$pollstats[$filter][$opt_key]['undefined'] = 1;
						}
					}
				}
			}

			$set[] = " pollstats.$filter = '". json_encode($pollstats[$filter], JSON_UNESCAPED_UNICODE). "'";

		} // end of foreach $support_chart

		//
		if($update_pollstat_record)
		{
			$set = join($set, " , ");
			$pollstats_update_query =
			"
				UPDATE
					pollstats
				SET
					$set
				WHERE
					pollstats.post_id = $poll_id AND
					pollstats.type    = '$validation'
				-- update poll stat result
				-- stat_polls::set_poll_result()
			";
			$pollstats_update = \lib\db::query($pollstats_update_query);
		}
		else
		{
			// insert record
			$set[] =  " pollstats.port    = $port ";
			$set[] =  " pollstats.subport = $subport ";
			$set[] =  " pollstats.post_id = $poll_id ";
			$set[] =  " pollstats.type    = '$validation' ";

			$set = join($set, " , ");
			$query =
			"
				INSERT INTO
					pollstats
				SET
					$set
			";
			$set_result = \lib\db::query($query);
		}
		return true;
	}
}
?>