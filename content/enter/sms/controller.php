<?php
namespace content\enter\sms;

class controller extends \content\main\controller
{
	/**
	 * check route of sms parts
	 * @return [type] [description]
	 */
	function _route()
	{
		\lib\storage::set_api(true);

		$service = \lib\utility::get('service');
		$type    = \lib\utility::get('type');
		$uid     = \lib\utility::get('uid');

		if(!$service || !$uid)
		{
			\lib\error::access('Hi Dear. Do you wanna something!?');
		}
		if($type === 'recieve' || $type === 'delivery')
		{
			// do nothing
		}
		else
		{
			\lib\error::access("SMS");
		}

		switch ($service)
		{
			case 'kavenegar':
				// if uid of kavenegar is incorrect, show error
				if($uid !== '201700001')
				{
					\lib\error::access("SMS");
				}

				if($type === 'recieve')
				{
					$from      = \lib\utility::request('from');
					$to        = \lib\utility::request('to');
					$message   = \lib\utility::request('message');
					$messageid = \lib\utility::request('messageid');

					$this->model()->kavenegar_recieve($from, $to, $message, $messageid);
				}
				elseif($type === 'delivery')
				{
					$messageid = \lib\utility::request('messageid');
					$status    = \lib\utility::request('status');

					$this->model()->kavenegar_delivery($messageid, $status);
				}
				break;

			default:
				\lib\error::access("You are in future");
				break;
		}
	}
}
?>