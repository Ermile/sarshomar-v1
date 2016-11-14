<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		/**
		 * check the support filter and make all filter array
		 * to route all filter
		 */
		$support_filter = \lib\db\filters::support_filter();
		$property = [];

		$property['search'] = ["/^.*$/", true, 'search'];
		$property['page']   = ["/^\d+$/", true, 'page'];

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
			'url'      => "/\$/",
			'property' => $property
		]
		);
	}
}
?>