<?php
namespace content\poll;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("poll","poll")->ALL("/^sp\_([". self::$shortURL. "]+)$/");
		$this->get("poll","poll")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->get("poll","poll")->ALL("/^\\$([". self::$shortURL. "]+)$/");

		// $this->post("save_answer")->ALL("/^\\$\/(([". self::$shortURL. "]+)(\/(.+))?)$/");
		$this->post("save_answer")->ALL("/.*/");
		$check_status = $this->access('admin','admin', 'view') ? false : true ;

		if($this->model()->get_posts(false, null, ['check_status' => $check_status, 'check_language' => false, 'post_type' => ['poll', 'survey']]))
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