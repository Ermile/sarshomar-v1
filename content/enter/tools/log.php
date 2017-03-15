<?php
namespace content\enter\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait log
{
	public function log($_caller, $_data = null)
	{
		$log_meta =
		[
			'meta' =>
			[
				'data'    => $_data,
				'mobile'  => $this->mobile,
				'input'   => utility::post(),
				'session' => $_SESSION,
			]
		];
		\lib\db\logs::set($_caller, null, $log_meta);
	}



	/**
	 * set counter of caller log
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public function counter($_caller)
	{

	}
}
?>