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
		$property               = [];

		$property['search']     = ["/^.*$/", true, 'search'];
		// $property['page']       = ["/^\d+$/", true, 'page'];
		// // public
		// $property['pollcat']    = ["/^civility|sarshomar$/", true, 'pollcat'];
		// $property['pollgender'] = ["/^poll|quiz|survey$/", true, 'pollgender'];
		// // question type
		// $property['polltype']   = ["/(multiplechoice|descriptive|notification|upload|starred|numerical|sort|,)+/", true, 'polltype'];
		// $property['status']     = ["/^expired|publish$/",true,'status'];


		// foreach ($support_filter as $key => $value) {
		// 	$reg = "/^.*$/";
		// 	if(is_array($value))
		// 	{
		// 		$reg = "/^". join($value, "|"). "$/";
		// 	}
		// 	$property[$key] = [$reg, true, $key];
		// }

		if(substr(\lib\router::get_url(), 0, 1) != '$')
		{
			\lib\router::set_url(trim('$/' . \lib\router::get_url(),'/'));
		}
		
		$this->get("search", "search")->ALL(
		[
			'url'      => "/^\\$(|\/search\=([^\/]+))$/",
			'property' => $property
		]
		);

		$this->post("search")->ALL("/.*/");
	}
}
?>