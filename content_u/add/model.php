<?php
namespace content_u\add;
use \content_api\poll\tools;

class model extends \content_u\home\model
{
	use tools\config;
	/**
	 * use the api model
	 */
	use tools\get
	{
		tools\get::get_poll as get_edit;
	}
}
?>