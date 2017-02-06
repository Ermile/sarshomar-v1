<?php
namespace content\referer\instagram;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	function post_instagram()
	{
		return \lib\utility\token::verify(utility::post('token'), (int) $_SESSION['user']['id']);
	}
}