<?php
namespace content\referer\token;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	function post_token()
	{
		return \lib\utility\token::verify(utility::post('token'), (int) $_SESSION['user']['id']);
	}
}