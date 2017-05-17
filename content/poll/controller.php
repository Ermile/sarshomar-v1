<?php
namespace content\poll;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		$this->get("poll","poll")->ALL("/^sp\_([". self::$shortURL. "]+)$/");
		$this->get("poll","poll")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->get("poll","poll")->ALL("/^\\$([". self::$shortURL. "]+)$/");

		// $this->post("save_answer")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->post("save_answer")->ALL("/.*/");
		$check_status = $this->access('admin:admin:view') ? false : true ;

		$load_poll =
		[
			'post_status'    => self::$accept_poll_status,
			'check_status'   => $check_status,
			'check_language' => false,
			'post_type'      => ['poll', 'survey']
		];
		if($this->model()->get_posts(false, null, $load_poll))
		{
			$this->get("realpath","poll")->ALL("/.*/");
			return;
		}
		else
		{
			$this->model()->check_url();
		}

	}
}
?>