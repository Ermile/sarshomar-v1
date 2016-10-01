<?php
namespace content\home;
use \lib\saloos;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		if(\lib\router::get_url(0) == '$')
		{
			\lib\router::set_controller("\\content\\knowledge\\controller");
			return ;
		}

		$this->post("random_result")->ALL("");

		$this->get("poll","poll")->ALL(
		[
		'property' =>
		[
			"knowledge" => ["/^(knowledge)$/", true, 'knowledge'],
			"sp_"       => ["/^sp\_[23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+$/", true, 'sp_'],
			"title"     => ["/^(.*)$/", true, 'title']
		]
		]);
	}
}
?>