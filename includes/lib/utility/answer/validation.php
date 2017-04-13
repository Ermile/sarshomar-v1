<?php
namespace lib\utility\answer;
use \lib\db;
use \lib\debug;
use \lib\utility;
use \lib\db\ranks;
use \lib\db\options;
use \lib\utility\users;
use \lib\db\polldetails;
use \lib\utility\profiles;
use \lib\utility\shortURL;
use \lib\utility\stat_polls;

trait validation
{

	/**
	 * check user verify
	 * set self::$validation
	 * return true or false
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function user_validataion($_user_id)
	{
		$save_offline_chart = true;
		self::$user_verify = users::get_user_verify($_user_id);
		switch (self::$user_verify)
		{
			case 'complete':
			case 'mobile':
				self::$validation  = 'valid';
				break;

			case 'uniqueid':
				self::$validation  = 'invalid';
				break;

			case 'unknown':
			default:
				self::$validation   = null;
				$save_offline_chart = false;
				break;
		}
		return $save_offline_chart;
	}




	/**
	 * change the user validation
	 * the user was in 'awaiting' status and
	 * we save all answers of this user in 'invalid' type of poll stats
	 * now the user active her account
	 * we change all stats the user was answered to it to 'valid' status
	 *
	 * @param      <type>  $_args['user_id']  The user identifier
	 */
	public static function change_user_validation_answers($_user_id)
	{

		// get all user answer to poll
		$invalid_answers    = polldetails::get($_user_id);
		$save_offline_chart = self::user_validataion($_user_id);
		foreach ($invalid_answers as $key => $value)
		{
			$check = true;

			if(!array_key_exists('post_id', $value)) 		$check = false;
			if(!array_key_exists('validstatus', $value)) 	$check = false;
			if(!array_key_exists('opt', $value)) 			$check = false;
			if(!array_key_exists('post_id', $value)) 		$check = false;
			if(!array_key_exists('opt', $value)) 			$check = false;
			if(!array_key_exists('port', $value)) 			$check = false;
			if(!array_key_exists('subport', $value)) 		$check = false;

			if(!$check)
			{
				continue;
			}
			// check validstatus
			// we just update invalid answers to valid mod
			if(is_null($value['validstatus']) && $value['status'] === 'enable')
			{
				// opt = 0 means the user skipped the poll and neddless to update chart
				// opt = null means the user answers the other text (descriptive mode) needless to update chart
				if(intval($value['opt']) > 0 && $value['opt'])
				{
					// update users profile
					$plus_valid_chart =
					[
						'validation'  => self::$validation,
						'poll_id'     => $value['post_id'],
						'opt_key'     => $value['opt'],
						'user_id'     => $_user_id,
						'update_mode' => true,
						'user_verify' => self::$user_verify,
						'port'        => $value['port'],
						'subport'     => $value['subport'],
					];
					$update_chart =  stat_polls::set_poll_result($plus_valid_chart);
					if($update_chart)
					{
						\lib\db\ranks::plus($value['post_id'], 'vote');
					}
				}
				elseif(intval($value['opt']) === 0)
				{
					\lib\db\ranks::plus($value['post_id'], 'skip');
				}
			}
		}

		if(self::$user_verify === 'mobile' || self::$user_verify === 'complete')
		{
			$query = "UPDATE polldetails SET validstatus = 'valid' WHERE user_id = $_user_id";
			db::query($query);
		}

		return (debug::$status) ? true : false;
	}


	/**
	 * user add filter
	 * we change old answer to this filter
	 *
	 * @param      array  $_options  The options
	 */
	public static function user_add_filter($_options = [])
	{
		// not now !
		return false;

		if(!is_array($_options) || !isset($_options['user_id']) || !isset($_options['old_filter']) || !isset($_options['new_filter']))
		{
			return false;
		}

		$old_filter    = \lib\db\filters::get($_options['old_filter']);
		$new_filter    = \lib\db\filters::get($_options['new_filter']);
		$saved_answers = polldetails::get($_options['user_id']);
		var_dump($saved_answers);
		foreach ($new_filter as $key => $value)
		{
			if(array_key_exists($key, $old_filter))
			{
				if($value && is_null($old_filter[$key]))
				{

					var_dump($key);
					var_dump($value);
				}
			}
		}

		// var_dump($old_filter, $new_filter);
		var_dump($_options);exit();

		// get all user answer to poll
		$save_offline_chart = self::user_validataion($_user_id);

		foreach ($invalid_answers as $key => $value)
		{
			$check = true;

			if(!array_key_exists('post_id', $value)) 		$check = false;
			if(!array_key_exists('validstatus', $value)) 	$check = false;
			if(!array_key_exists('opt', $value)) 			$check = false;
			if(!array_key_exists('post_id', $value)) 		$check = false;
			if(!array_key_exists('opt', $value)) 			$check = false;
			if(!array_key_exists('port', $value)) 			$check = false;
			if(!array_key_exists('subport', $value)) 		$check = false;
			if(!array_key_exists('subport', $value)) 		$check = false;

			if(!$check)
			{
				continue;
			}
			// check validstatus
			// we just update invalid answers to valid mod
			if(is_null($value['validstatus']) && $value['status'] === 'enable')
			{
				// opt = 0 means the user skipped the poll and neddless to update chart
				// opt = null means the user answers the other text (descriptive mode) needless to update chart
				if($value['opt'] !== 0 && $value['opt'] !== null)
				{
					// update users profile
					$plus_valid_chart =
					[
						'validation'  => self::$validation,
						'poll_id'     => $value['post_id'],
						'opt_key'     => $value['opt'],
						'user_id'     => $_user_id,
						'update_mode' => true,
						'user_verify' => self::$user_verify,
						'port'        => $value['port'],
						'subport'     => $value['subport'],
					];
					$update_chart =  stat_polls::set_poll_result($plus_valid_chart);
					if($update_chart)
					{
						\lib\db\ranks::plus($value['post_id'], 'vote');
					}
				}
				else
				{
					\lib\db\ranks::plus($value['post_id'], 'skip');
				}
			}
		}
		if(self::$user_verify === 'mobile' || self::$user_verify === 'complete')
		{
			$query = "UPDATE polldetails SET validstatus = 'valid' WHERE user_id = $_user_id";
			db::query($query);
		}

		return (debug::$status) ? true : false;
	}
}
?>