<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait resend
{
	public function step_resend()
	{

		// [
		// 	'telegram',
		// 	'call',
		// 	'main_sms',
		// 	'secondary_sms',
		// ];
		$rate = 0;

		$log_caller = \lib\db\logitems::caller('user:verification:code');
		$log_where  =
		[
			'user_id'    => $this->user_id,
			'log_status' => 'enable',
			'logitem_id' => $log_caller,
			'limit'      => 1,
		];
		$saved_code = \lib\db\logs::get($log_where);
		if(empty($saved_code) || !isset($saved_code['log_meta']['type']))
		{
			$rate = 0;
		}
		else
		{
			switch ($saved_code['log_meta']['type'])
			{
				case 'telegram':
				case 'code':
				case 'main_sms':
				case 'secondary_sms':
					$key = array_search($saved_code['log_meta']['type'], $this->resend_rate);
					if($key === false)
					{
						$rate = 0;
					}
					else
					{
						$rate = $key + 1;
					}
					break;

				default:
					$rate = 0;
					break;
			}
		}

		if(isset($rate))
		{
			if(isset($this->resend_rate[$rate]))
			{
				return $this->resend_rate[$rate];
			}
		}
		return false;
	}
}
?>