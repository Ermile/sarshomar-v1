<?php
namespace content\enter\tools\step;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait resend
{
	public function step_resend()
	{
		$rate          = 0;
		$saved_code_id = false;
		$log_caller = \lib\db\logitems::caller('user:verification:code');
		$log_where  =
		[
			'user_id'    => $this->user_id,
			'log_status' => 'enable',
			'logitem_id' => $log_caller,
			'limit'      => 1,
		];
		$saved_code = \lib\db\logs::get($log_where);
		if(empty($saved_code) || !isset($saved_code['log_desc']) || !isset($saved_code['id']))
		{
			$rate = 0;
		}
		else
		{
			$saved_code_id = $saved_code['id'];

			switch ($saved_code['log_desc'])
			{
				case 'telegram':
				case 'call':
				case 'sms1':
				case 'sms2':
					$key = array_search($saved_code['log_desc'], $this->resend_rate);
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
				if($saved_code_id)
				{
					\lib\db\logs::update(['log_desc' => $this->resend_rate[$rate]], $saved_code_id);
				}
				return $this->resend_rate[$rate];
			}
		}
		return false;
	}
}
?>