<?php
namespace lib\utility\chart;
use \lib\debug;

trait refresh
{

	public static $chart   = [];
	public static $total   = 0;
	public static $time    = 0;
	public static $skip    = [];
	public static $poll_id = 0;

	public static function refresh_all()
	{
		$query = "SELECT post_id FROM polldetails GROUP BY post_id";
		$poll_ids = \lib\db::get($query, 'post_id');
		self::$time = time();
		foreach ($poll_ids as $key => $value)
		{
			self::$chart   = [];
			self::$total   = 0;
			self::$time    = 0;
			self::$skip    = [];
			self::$poll_id = 0;
			self::refresh($value);
		}

	}

	/**
	 * refresh chart stats
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function refresh($_poll_id)
	{
		self::$poll_id = (int) $_poll_id;
		$start = time();
		if(!$_poll_id || !is_numeric($_poll_id))
		{
			return fales;
		}

		$all_answers = \lib\db::get("SELECT * FROM polldetails WHERE post_id = $_poll_id AND status = 'enable' ORDER BY polldetails.opt ASC ");
		$all_user_id    = [];
		$all_profile_id = [];

		if(!is_array($all_answers))
		{
			return false;
		}

		$all_user_id = array_column($all_answers, 'user_id');
		$all_user_id = array_unique($all_user_id);
		$all_user_id = array_filter($all_user_id);

		if(!empty($all_user_id))
		{
			$all_user_id      = implode(',', $all_user_id);
			$all_user_data    = \lib\db::get("SELECT * FROM users WHERE users.id IN ($all_user_id) ");
			$all_user_data_id = array_column($all_user_data, 'id');

			$all_profile_id   = array_column($all_user_data, 'filter_id');
			$all_profile_id   = array_unique($all_profile_id);
			$all_profile_id   = array_filter($all_profile_id);

			$all_user_data    = array_combine($all_user_data_id, $all_user_data);
		}

		if(!empty($all_profile_id))
		{
			$all_profile_id      = implode(',', $all_profile_id);
			$all_profile_data    = \lib\db::get("SELECT * FROM filters WHERE filters.id IN ($all_profile_id) ");
			$all_profile_data_id = array_column($all_profile_data, 'id');
			$all_profile_data    = array_combine($all_profile_data_id, $all_profile_data);
		}

		$user_ids = [];

		foreach ($all_answers as $i => $value)
		{
			if(!self::check_value($value))
			{
				continue;
			}
			$user_data = (isset($all_user_data[$value['user_id']])) ? $all_user_data[$value['user_id']] : [];
			// $user_data = \lib\db\users::get($value['user_id']);
			if(!self::check_value($user_data, 'user'))
			{
				continue;
			}

			$profile = (isset($all_profile_data[$all_user_data[$value['user_id']]['filter_id']])) ? $all_profile_data[$all_user_data[$value['user_id']]['filter_id']] : [];
			// $profile = (isset($all_profile_data[$value['profile']])) ? $all_profile_data[$value['profile']] : [];
			// $profile   = \lib\db\filters::get($value['profile']);

			if(!self::check_value($profile, 'profile'))
			{
				continue;
			}

			array_push($user_ids, $user_data['id']);

			$args =
			[
				'port'        => $value['port'],
				'subport'     => $value['subport'],
				'type'        => $value['validstatus'],
				'opt'         => "opt_".$value['opt'],
				'user_verify' => $user_data['user_verify'],
				'profile'     => $profile,
			];

			self::chart_data($args);
		}

		// $old_result = \lib\db\pollstats::get($_poll_id,['validation' => 'valid']);


		$temp = [];
		$i = 0;
		$set = [];
		foreach (self::$chart as $port => $chart)
		{
			foreach ($chart as $validstatus => $data)
			{
				$check_update_or_insert = (int) \lib\db::get(
				"SELECT id FROM pollstats
				 WHERE post_id = $_poll_id
				 AND port = '$port'
				 AND type = '$validstatus'  LIMIT 1", 'id', true);

				if($check_update_or_insert)
				{
					$temp[$i]['condition_query'] = " id = $check_update_or_insert ";
				}
				else
				{
					$temp[$i]['insert_query'] = " INSERT INTO pollstats  ";
				}

				$temp[$i]['port']    = $port;
				$temp[$i]['subport'] = null;
				$temp[$i]['type']    = $validstatus;
				foreach ($data as $field => $value)
				{
					$temp[$i][$field] = json_encode($value, JSON_UNESCAPED_UNICODE);
				}
				$i++;
			}
		}

		foreach ($temp as $key => $data)
		{
			$insert_query    = null;
			$condition_query = null;

			$set = [];
			if(is_array($data))
			{
				foreach ($data as $field => $value)
				{
					if($field === 'condition_query')
					{
						$condition_query = $value;
						continue;
					}

					if($field === 'insert_query')
					{
						$insert_query = $value;
						continue;
					}

					if($value === null)
					{
						$set[] = " pollstats.$field = NULL";
					}
					else
					{
						$set[] = " pollstats.$field = '$value' ";
					}
				}
			}


			if(!empty($set) && ($insert_query || $condition_query))
			{

				$set = implode(',', $set);
				if($insert_query)
				{
					$query = "INSERT INTO pollstats SET pollstats.post_id = $_poll_id, $set ";
				}
				elseif($condition_query)
				{
					$query = "UPDATE pollstats SET  $set WHERE $condition_query";
				}

				\lib\db::query($query);
				\lib\db::query("UPDATE ranks set ranks.vote =
								(SELECT IFNULL(SUM(pollstats.total), 0) FROM pollstats WHERE pollstats.post_id = $_poll_id)
								WHERE ranks.post_id = $_poll_id
								LIMIT 1");
				\lib\db::query("UPDATE ranks set ranks.skip =
					(
						SELECT 	IFNULL(COUNT(polldetails.id), 0)
						FROM polldetails
						WHERE polldetails.post_id = ranks.post_id
						AND polldetails.opt = 0
						AND polldetails.status = 'enable'
						AND polldetails.validstatus IS NOT NULL
					) LIMIT 1");

			}
		}
	}


	/**
	 * set offline chart
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function chart_data($_args)
	{

		$temp = [];

		if($_args['opt'] === 'opt_0')
		{
			if(isset(self::$skip[self::$poll_id]))
			{
				self::$skip[self::$poll_id]++;
			}
			else
			{
				self::$skip[self::$poll_id] = 1;
			}
			return;
		}

		$validstatus = null;

		if($_args['user_verify'] === 'complete' || $_args['user_verify'] === 'mobile')
		{
			$validstatus = 'valid';
		}
		elseif($_args['user_verify'] === 'uniqueid')
		{
			$validstatus = 'invalid';
		}
		else
		{
			return;
		}

		if(isset(self::$chart[$_args['port']][$validstatus]['total']))
		{
			self::$chart[$_args['port']][$validstatus]['total']++;
		}
		else
		{
			self::$chart[$_args['port']][$validstatus]['total'] = 1;
		}

		if(isset(self::$chart[$_args['port']][$validstatus]['result'][$_args['opt']]))
		{
			self::$chart[$_args['port']][$validstatus]['result'][$_args['opt']]++;
		}
		else
		{
			self::$chart[$_args['port']][$validstatus]['result'][$_args['opt']] = 1;
		}


		foreach ($_args['profile'] as $filter => $male)
		{

			if($male)
			{
				if(isset(self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']][$male]))
				{
					self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']][$male]++;
				}
				else
				{
					self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']][$male] = 1;
				}
			}
			else
			{
				if(isset(self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']]['unknown']))
				{
					self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']]['unknown']++;
				}
				else
				{
					self::$chart[$_args['port']][$validstatus][$filter][$_args['opt']]['unknown'] = 1;
				}

			}
		}
	}


	/**
	 * check index of array
	 *
	 * @param      <type>   $_data  The data
	 * @param      string   $_type  The type
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private static function check_value(&$_data, $_type = 'polldetails')
	{
		$check = true;
		switch ($_type)
		{
			case 'polldetails':
				if(!array_key_exists('id', $_data))					$check = false;
				if(!array_key_exists('post_id', $_data))			$check = false;
				if(!array_key_exists('user_id', $_data))			$check = false;
				if(!array_key_exists('port', $_data))				$check = false;
				if(!array_key_exists('validstatus', $_data))		$check = false;
				// if(!array_key_exists('subport', $_data))			$check = false;
				if(!array_key_exists('opt', $_data))				$check = false;
				// if(!array_key_exists('answertype', $_data))			$check = false;
				// if(!array_key_exists('type', $_data))				$check = false;
				// if(!array_key_exists('txt', $_data))				$check = false;
				if(!array_key_exists('profile', $_data))			$check = false;
				if(!array_key_exists('status', $_data))				$check = false;
				// if(!array_key_exists('visitor_id', $_data))			$check = false;
				// if(!array_key_exists('insertdate', $_data))			$check = false;
				// if(!array_key_exists('date_modified', $_data))		$check = false;

				break;

			case 'profile':
				// if(!array_key_exists('id', $_data)) 				$check = false;
				// if(!array_key_exists('count', $_data)) 				$check = false;
				// if(!array_key_exists('gender', $_data)) 			$check = false;
				// if(!array_key_exists('marrital', $_data)) 			$check = false;
				// if(!array_key_exists('internetusage', $_data)) 		$check = false;
				// if(!array_key_exists('graduation', $_data)) 		$check = false;
				// if(!array_key_exists('degree', $_data)) 			$check = false;
				// if(!array_key_exists('course', $_data)) 			$check = false;
				// if(!array_key_exists('age', $_data)) 				$check = false;
				// if(!array_key_exists('agemin', $_data)) 			$check = false;
				// if(!array_key_exists('agemax', $_data)) 			$check = false;
				// if(!array_key_exists('range', $_data)) 				$check = false;
				// if(!array_key_exists('country', $_data)) 			$check = false;
				// if(!array_key_exists('province', $_data)) 			$check = false;
				// if(!array_key_exists('city', $_data)) 				$check = false;
				// if(!array_key_exists('employmentstatus', $_data)) 	$check = false;
				// if(!array_key_exists('housestatus', $_data)) 		$check = false;
				// if(!array_key_exists('religion', $_data)) 			$check = false;
				// if(!array_key_exists('language', $_data)) 			$check = false;
				// if(!array_key_exists('industry', $_data)) 			$check = false;
				//  if(!array_key_exists('country', $_data)) 			$check = false;
				// if(!array_key_exists('province', $_data)) 			$check = false;
				// if(!array_key_exists('city', $_data)) 				$check = false;
				// if(!array_key_exists('employmentstatus', $_data)) 	$check = false;
				// if(!array_key_exists('housestatus', $_data)) 		$check = false;
				// if(!array_key_exists('religion', $_data)) 			$check = false;
				// if(!array_key_exists('language', $_data)) 			$check = false;
				// if(!array_key_exists('industry', $_data)) 			$check = false;


				if(!array_key_exists('gender', $_data)) 			$_data['gender'] = null;
				if(!array_key_exists('marrital', $_data)) 			$_data['marrital'] = null;
				if(!array_key_exists('internetusage', $_data)) 		$_data['internetusage'] = null;
				if(!array_key_exists('graduation', $_data)) 		$_data['graduation'] = null;
				if(!array_key_exists('degree', $_data)) 			$_data['degree'] = null;
				if(!array_key_exists('course', $_data)) 			$_data['course'] = null;
				if(!array_key_exists('age', $_data)) 				$_data['age'] = null;
				if(!array_key_exists('agemin', $_data)) 			$_data['agemin'] = null;
				if(!array_key_exists('agemax', $_data)) 			$_data['agemax'] = null;
				if(!array_key_exists('range', $_data)) 				$_data['range'] = null;
				if(!array_key_exists('country', $_data)) 			$_data['country'] = null;
				if(!array_key_exists('province', $_data)) 			$_data['province'] = null;
				if(!array_key_exists('city', $_data)) 				$_data['city'] = null;
				if(!array_key_exists('employmentstatus', $_data)) 	$_data['employmentstatus'] = null;
				if(!array_key_exists('housestatus', $_data)) 		$_data['housestatus'] = null;
				if(!array_key_exists('religion', $_data)) 			$_data['religion'] = null;
				if(!array_key_exists('language', $_data)) 			$_data['language'] = null;
				if(!array_key_exists('industry', $_data)) 			$_data['industry'] = null;

				unset($_data['id']);
				unset($_data['count']);
				unset($_data['agemin']);
				unset($_data['agemax']);

				break;

			case 'user':
				if(!array_key_exists('id', $_data))					$check = false;
				// if(!array_key_exists('user_mobile', $_data))		$check = false;
				// if(!array_key_exists('user_email', $_data))			$check = false;
				// if(!array_key_exists('user_username', $_data))		$check = false;
				// if(!array_key_exists('user_pass', $_data))			$check = false;
				// if(!array_key_exists('user_displayname', $_data))	$check = false;
				// if(!array_key_exists('user_meta', $_data))			$check = false;
				// if(!array_key_exists('user_status', $_data))		$check = false;
				// if(!array_key_exists('user_permission', $_data))	$check = false;
				// if(!array_key_exists('user_createdate', $_data))	$check = false;
				// if(!array_key_exists('user_parent', $_data))		$check = false;
				// if(!array_key_exists('user_validstatus', $_data))	$check = false;
				if(!array_key_exists('filter_id', $_data))			$check = false;
				// if(!array_key_exists('user_port', $_data))			$check = false;
				// if(!array_key_exists('user_trust', $_data))			$check = false;
				if(!array_key_exists('user_verify', $_data))		$check = false;
				// if(!array_key_exists('date_modified', $_data))		$check = false;
				break;
			default:
				return false;
				break;
		}
		return $check;
	}
}
?>
