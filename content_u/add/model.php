<?php
namespace content_u\add;
use \content_api\poll\tools as poll;
use \content_api\upload\tools as upload;

class model extends \content_u\home\model
{
	use filter\model;
	use publish\model;

	use poll\config;
	use poll\put;
	use poll\post;
	use poll\delete;
	/**
	 * use the api model
	 */
	use poll\get
	{
		poll\get::get_poll as get_edit;
	}

}
?>