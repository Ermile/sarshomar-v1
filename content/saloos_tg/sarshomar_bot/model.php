<?php
namespace content\saloos_tg\sarshomar_bot;
use \content_api\v1;
class model extends \lib\mvc\model{
	public $api_mode = true;
	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\tools\add;

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\poll\tools\delete;

	use \content_api\v1\poll\search\tools\search;
}
?>