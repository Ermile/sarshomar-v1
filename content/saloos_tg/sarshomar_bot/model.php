<?php
namespace content\saloos_tg\sarshomar_bot;
use \content_api\v1;
class model extends \lib\mvc\model{
	use \content_api\v1\home\tools\ready;
	use \content_api\v1\poll\tools\add {
		\content_api\v1\poll\tools\add::add as add_poll;
	}

	use \content_api\v1\poll\tools\get {
		\content_api\v1\poll\tools\get::get as get_poll;
	}

	use \content_api\v1\poll\tools\delete;
}
?>