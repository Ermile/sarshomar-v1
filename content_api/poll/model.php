<?php
namespace content_api\poll;

class model extends \mvc\model
{
	
	public $shortURL = "23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";

	use tools\config;
	use tools\get;
	use tools\post;
	use tools\put;
	use tools\delete;
}
?>