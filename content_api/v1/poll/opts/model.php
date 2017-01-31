<?php
namespace content_api\v1\poll\opts;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{

	use \content_api\v1\poll\opts\tools\get;

	public function get_opts($_args)
	{
		return $this->poll_opts();
	}

}
?>