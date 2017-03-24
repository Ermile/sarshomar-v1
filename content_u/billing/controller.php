<?php
namespace content_u\billing;

class controller extends  \content_u\main\controller
{

	public function _route()
	{
		//
		// 	$money =
		// 	[
		// 		99,
		// 		100, // 100 000
		// 		200,
		// 		300,
		// 		1000, // 1 000 000
		// 		5000, // 5 000 000
		// 		10000, // 10 000 000
		// 		15000, // 15 000 000
		// 		20000, // 20 000 000
		// 		50000, // 50 000 000
		// 		100000, // 100 000 000
		// 		200000, // 200 000 000
		// 		300000, // 300 000 000

		// 	];
		// 	foreach ($money as $key => $money)
		// 	{
		// 		$gift = \lib\utility\gift::gift($money);
		// 		var_dump(number_format($money). " => " . number_format($gift));
		// 	}
		// 	exit();
		//
		parent::_route();

		$this->get("billing", "billing")->ALL();
		$this->post("billing")->ALL();
		$this->get("verify")->ALL("/billing\/verify\/(zarinpal)/");
	}

}

?>