<?php
namespace content_u\add;
use \content_api\poll\tools;

class model extends \content_u\home\model
{
	/**
	 * to load tools function
	 */
	use tools\config;

	/**
	 * use the api tools 
	 */
	use tools\post
	{
		tools\post::post_poll as post_add;
	}

	/**
	 * use the api model
	 */
	use tools\put
	{
		tools\put::put_poll as post_edit;
	}

	/**
	 * use the api model
	 */
	use tools\get
	{
		tools\get::get_poll as get_edit;
	}
}
?>