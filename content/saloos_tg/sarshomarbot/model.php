<?php
namespace content\saloos_tg\sarshomarbot;
use \content_api\v1;
class model extends \lib\mvc\model{
	public $api_mode = true;

	use \lib\mvc\models\account;

	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\tools\add;

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\poll\tools\delete;

	use \content_api\v1\poll\search\tools\search;

	use \content_api\v1\poll\status\tools\get;

	use \content_api\v1\poll\status\tools\set;

	use \content_api\v1\poll\answer\tools\add;

	use \content_api\v1\file\tools\link;

	use \content_api\v1\poll\answer\tools\get;

	use \content_api\v1\poll\answer\tools\add;

	use \content_api\v1\poll\answer\tools\delete;

	use \content_api\v1\poll\answers\tools\get;

	use \content_api\v1\profile\tools\get;

	use \content_api\v1\profile\tools\set;

}
?>