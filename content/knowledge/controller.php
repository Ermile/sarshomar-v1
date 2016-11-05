<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get(false,"knowledge")->ALL("$");
		// $this->post("search")->ALL("$");

		/**
		 * check the support filter and make all filter array
		 * to route all filter
		 */
		$support_filter = \lib\db\filters::support_filter();
		$property = [];

		$property['search']   = ["/^.*$/", true, 'search'];

		foreach ($support_filter as $key => $value) {
			$reg = "/^.*$/";
			if(is_array($value))
			{
				$reg = "/^". join($value, "|"). "$/";
			}
			$property[$key] = [$reg, true, $key];
		}

		$this->get("search", "search")->ALL(
		[
			'url'      => "/^\\$/",
			'property' => $property
		]
		);
	}
}
?>