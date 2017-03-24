<?php
namespace content\enter\sms;

class view extends \mvc\view
{

	/**
	 * config
	 */
	public function config()
	{
		$this->include->css    = true;
		$this->include->js     = false;
		$this->data->bodyclass = 'unselectable';
		$this->data->bodyclass .= ' bg'. rand(1, 17);

		$this->data->getParam  =
		[
			'service'   => \lib\utility::get('service'),
			'type'      => \lib\utility::get('type'),
			'uid'       => \lib\utility::get('uid'),

			'from'      => \lib\utility::request('from'),
			'to'        => \lib\utility::request('to'),
			'message'   => \lib\utility::request('message'),
			'messageid' => \lib\utility::request('messageid'),
			'status'    => \lib\utility::request('status'),
		];
	}
}
?>