<?php
namespace lib\utility;

class report
{

	/**
	 * report a post
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function set($_poll_id, $_user_id, $_caller)
	{
		$log_item    = \lib\db\logitems::caller($_caller);
		$log_item_id = null;

		if(!$log_item || !isset($log_item['id']))
		{
			$log_item = self::auto_insert($_caller);
			if($log_item === false)
			{
				return false;
			}
			elseif(is_numeric($log_item))
			{
				$log_item_id = $log_item;
			}
			else
			{
				return false;
			}
		}
		if($log_item_id === null)
		{
			$log_item_id = $log_item['id'];
		}

		$insert_log =
		[
			'logitem_id'     => $log_item_id,
			'user_id'        => $_user_id,
			'log_data'       => $_poll_id,
			'log_status'     => 'enable',
			'log_createdate' => date("Y-m-d H:i:s")
		];
		\lib\db\logs::insert($insert_log);

		$plus = 1;
		if(isset($logitem['logitem_priority']))
		{
			switch ($logitem['logitem_priority'])
			{
				case 'low':
					$plus = 1;
					break;

				case 'medium':
					$plus = 2;
					break;

				case 'high':
					$plus = 3;
					break;

				case 'critical':
					$plus = 5;
					break;

				default:
					$plus = 1;
					break;
			}
		}
		// save post ranks
		\lib\db\ranks::plus($_poll_id, 'report', $plus);

		// save the user ranks
		\lib\db\userranks::plus($_user_id, 'goodreport');
	}


	/**
	 * auto insert report
	 *
	 * @param      <type>  $_caller  The caller
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function auto_insert($_caller)
	{
		$insert_log_items = [];

		// report the privacy
		$insert_log_items['privacy'] =
		[
			'logitem_type'     => 'report',
			'logitem_caller'   => 'privacy',
			'logitem_title'    => 'the privacy post',
			'logitem_priority' => 'high'
		];

		if(isset($insert_log_items[$_caller]))
		{
			$result = \lib\db\logitems::insert($insert_log_items[$_caller]);
			if($result)
			{
				return (int) \lib\db::insert_id(\lib\db::$link);
			}
		}
		return false;
	}
}
?>